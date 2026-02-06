<?php

declare(strict_types=1);

namespace Phaze\Storage\Services;

use Phaze\Common\Contracts\Authorisation\Authorisation as AuthorisationContract;
use Phaze\Common\Contracts\Authorisation\ClientApplicationCredentials as ClientApplicationCredentialsContract;
use Phaze\Common\Contracts\Authorisation\OAuth2Authoriser as OAuth2AuthenticatorContract;
use Phaze\Common\Contracts\RestClient as RestClientContract;
use Phaze\Common\Contracts\RestCommand;
use Phaze\Common\Exceptions\AuthorisationException;
use Phaze\Common\RestClient;
use Phaze\Storage\Exceptions\BlobStorageException;
use Phaze\Storage\Types\AccountName;

abstract class AbstractStorageService
{
    public const Version20231103 = '2023-11-03';

    public const VersionDefault = self::Version20231103;

    protected const GrantType = OAuth2AuthenticatorContract::ClientCredentialsGrantType;

    protected const Resource = OAuth2AuthenticatorContract::StorageAccountResource;

    private ?AuthorisationContract $authorisation = null;

    private ClientApplicationCredentialsContract $credentials;

    private AccountName $account;

    private RestClientContract $restClient;

    public function __construct(AccountName $account, ClientApplicationCredentialsContract $credentials, ?RestClientContract $client = null)
    {
        $this->credentials = $credentials;
        $this->account = $account;
        $this->restClient = $client ?? new RestClient();
    }

    final protected function checkAuthorisation(): void
    {
        if (null === $this->authorisation || $this->authorisation->hasExpired()) {
            try {
                $this->authorisation = $this->credentials->authorise(self::Resource, self::GrantType);
            } catch (AuthorisationException $err) {
                throw new BlobStorageException("Failed to obtain OAuth2 token for Azure service bus: {$err->getMessage()}", previous: $err);
            }
        }
    }

    final protected function sendCommand(RestCommand $command): mixed
    {
        $this->checkAuthorisation();

        try {
            return $this->restClient->send($command, $this->authorisation);
        } catch (AuthorisationException $err) {
            // just in case we were right on the cusp of expiry when we checked above
            $this->checkAuthorisation();
            return $this->restClient->send($command, $this->authorisation);
        }
    }

    public function account(): AccountName
    {
        return $this->account;
    }


    public function withAccount(AccountName $account): self
    {
        $clone = clone $this;
        $clone->account = $account;
        return $clone;
    }
}
