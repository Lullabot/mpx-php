<?php

namespace Lullabot\Mpx\Service\IdentityManagement;

use Lullabot\Mpx\Client;
use Lullabot\Mpx\DataService\IdInterface;
use Lullabot\Mpx\Token;

/**
 * Defines how a user authenticates with mpx, and how its token is sent.
 *
 * mpx supports more than one authentication flow. Legacy users sign in against
 * the identity management service and pass their token as a query parameter.
 * CTS/CVP service users exchange an OAuth client ID and secret for a JWT and
 * pass it in an Authorization header. A single flow owns both halves so that
 * the two can never be combined by accident.
 */
interface AuthenticationFlowInterface
{
    /**
     * Acquire a new token for a user.
     *
     * @param UserInterface $user     The user to authenticate as.
     * @param Client        $client   The client used to access mpx.
     * @param int|null      $duration (optional) The number of seconds the token should be valid for. Flows that do
     *                                not let the client choose a lifetime ignore this.
     *
     * @return Token A valid mpx authentication token.
     */
    public function acquire(UserInterface $user, Client $client, ?int $duration = null): Token;

    /**
     * Merge a token into an array of request options.
     *
     * @param Token            $token   The token to send.
     * @param array            $options The request options to merge into.
     * @param IdInterface|null $account (optional) The account to use as the account context for the request.
     *
     * @return array The updated request options.
     */
    public function apply(Token $token, array $options, ?IdInterface $account = null): array;

    /**
     * Return a stable identifier for a user under this flow.
     *
     * This is used for token cache keys and for sign-in lock names. It must be
     * unique per flow, so that two flows authenticating the same credentials
     * never share a cached token.
     */
    public function identifier(UserInterface $user): string;

    /**
     * Revoke a token, if this flow supports revoking.
     */
    public function revoke(Token $token, Client $client): void;
}
