<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Common\service;

use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Validator;

class AdminToken extends BaseService
{
    /**
     * @return Plain 返回不是字符串，需要toString()转化成字符串
     */
    public function generateToken($key, $value, $expire)
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::base64Encoded(getenv('TOKEN_KEY'));

        $now = new DateTimeImmutable();
        return $tokenBuilder
//            // Configures the issuer (iss claim)
//            ->issuedBy('http://example.com')
//            // Configures the audience (aud claim)
//            ->permittedFor('http://example.org')
//            // Configures the id (jti claim)
//            ->identifiedBy('4f1g23a12aa')
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            ->relatedTo(getenv('RELATED_TO'))
//            // Configures the time that the token can be used (nbf claim)
//            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify($expire))
            // Configures a new claim, called "uid"
            ->withClaim($key, $value)
            // Configures a new header, called "foo"
//            ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($algorithm, $signingKey);
    }

    // 判断是否已过期
    public function isExpired(string $token)
    {
        if ($this->getClaim($token, 'exp')->getTimestamp() < time()) {
            return true;
        }

        return false;
    }

    // 判断是否非法
    public function isMy(string $token)
    {
        $parser = new Parser(new JoseEncoder());

        $token = $parser->parse(
            $token
        );
        $validator = new Validator();
        if ($validator->validate($token, new RelatedTo(getenv('RELATED_TO')))) {
            return true;
        }

        return false;
    }

    public function getClaim(string $token, $key)
    {
        $parser = new Parser(new JoseEncoder());
        $info = $parser->parse(
            $token
        );

        return $info->claims()->get($key);
    }
}
