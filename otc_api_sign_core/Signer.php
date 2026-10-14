<?php
namespace OTC;

define('BasicDateFormat', 'Ymd\THis\Z');
define('Algorithm', 'SDK-HMAC-SHA256');
define('HeaderXDate', 'X-Sdk-Date');
define('HeaderHost', 'host');
define('HeaderAuthorization', 'Authorization');
define('HeaderContentSha256', 'X-Sdk-Content-Sha256');
define('HeaderContentSha256_UNSIGNEDPAYLOAD', 'UNSIGNED-PAYLOAD');
define('HXSecurityToken', 'X-Security-Token');

/**
 * Builds SDK-HMAC-SHA256 signatures and prepares signed HTTP requests.
 */
class Signer
{
  /** @var string Access key identifier sent in the Authorization header. */
  public $Key = '';
  /** @var string Secret key used to calculate the request signature. */
  public $Secret = '';
  /** @var string Optional security token for temporary credentials. */
  public $SecurityToken = '';

  /**
   * Encode a value using the escaping rules used by request signing.
   */
  function escape($string)
  {
    $entities = array('+', '%7E');
    $replacements = array('%20', '~');
    return str_replace($entities, $replacements, urlencode($string));
  }

  /**
   * Return a request header value without regard to the header name's case.
   */
  function findHeader($r, $header)
  {
    foreach ($r->headers as $key => $value) {
      if (!strcasecmp($key, $header)) {
        return $value;
      }
    }
    return NULL;
  }

  /**
   * Build the canonical request string used as input to the signature.
   * The body is SHA-256 hashed unless X-Sdk-Content-Sha256 is UNSIGNED-PAYLOAD.
   *
   * CanonicalRequest =
   *   HTTPRequestMethod + '\n' +
   *   CanonicalURI + '\n' +
   *   CanonicalQueryString + '\n' +
   *   CanonicalHeaders + '\n' +
   *   SignedHeaders + '\n' +
   *   HexEncode(Hash(RequestPayload))
   */
  function CanonicalRequest($r, $signedHeaders)
  {
    $CanonicalURI = $this->CanonicalURI($r);
    $CanonicalQueryString = $this->CanonicalQueryString($r);
    $canonicalHeaders = $this->CanonicalHeaders($r, $signedHeaders);
    $signedHeadersString = join(';', $signedHeaders);

    $hencode = $this->findHeader($r, HeaderContentSha256);

    // sign body if x-sdk-content-sha256
    // - is not set, or
    // - set to UNSIGNED-PAYLOAD
    // if (!$hash) {
    if (!$hencode or $hencode !== HeaderContentSha256_UNSIGNEDPAYLOAD) {
      $hencode = hash('sha256', $r->body);
    }

    return "$r->method\n$CanonicalURI\n$CanonicalQueryString\n$canonicalHeaders\n$signedHeadersString\n$hencode";
  }

  /**
   * Return the escaped, slash-terminated canonical URI for a request.
   */
  function CanonicalURI($r)
  {
    $pattens = explode('/', $r->uri);
    $uri = array();
    foreach ($pattens as $v) {
      array_push($uri, $this->escape($v));
    }
    $urlpath = join('/', $uri);
    if (substr($urlpath, -1) != '/') {
      $urlpath = $urlpath . '/';
    }
    return $urlpath;
  }

  /**
   * Return the query parameters sorted and escaped for signing.
   */
  function CanonicalQueryString($r)
  {
    $keys = array();
    foreach ($r->query as $key => $value) {
      array_push($keys, $key);
    }
    sort($keys);
    $a = array();
    foreach ($keys as $key) {
      $k = $this->escape($key);
      $value = $r->query[$key];
      if (is_array($value)) {
        sort($value);
        foreach ($value as $v) {
          $kv = "$k=" . $this->escape($v);
          array_push($a, $kv);
        }
      } else {
        $kv = "$k=" . $this->escape($value);
        array_push($a, $kv);
      }
    }
    return join('&', $a);
  }

  /**
   * Return the selected headers in canonical name:value form.
   */
  function CanonicalHeaders($r, $signedHeaders)
  {
    $headers = array();
    foreach ($r->headers as $key => $value) {
      $headers[strtolower($key)] = trim($value);
    }
    $a = array();
    foreach ($signedHeaders as $key) {
      array_push($a, $key . ':' . $headers[$key]);
    }
    return join("\n", $a) . "\n";
  }

  /**
   * Format request headers for CURLOPT_HTTPHEADER.
   */
  function curlHeaders($r)
  {
    $header = array();
    foreach ($r->headers as $key => $value) {
      array_push($header, strtolower($key) . ':' . trim($value));
    }
    return $header;
  }

  /**
   * Return request headers as an associative array, suitable for Guzzle.
   */
  function headersAsArray($r)
  {
    $headers = [];
    foreach ($r->headers as $key => $value) {
      $headers[strtolower($key)] = trim($value);
    }
    return $headers;
  }

  /**
   * Return request headers as a CRLF-delimited string for PHP streams.
   */
  function headersAsString($r)
  {
    $headers = $this->headersAsArray($r);
    $headerString = '';
    foreach ($headers as $key => $value) {
      if ($headerString !== '') {
        $headerString .= "\r\n";
      }
      $headerString .= $key . ': ' . $value;
    }
    return $headerString;
  }

  /**
   * Return all request header names in lowercase, sorted for signing.
   */
  function SignedHeaders($r)
  {
    $a = array();
    foreach ($r->headers as $key => $value) {
      array_push($a, strtolower($key));
    }
    sort($a);
    return $a;
  }

  /**
   * Create the timestamped string-to-sign from a canonical request.
   */
  function StringToSign($canonicalRequest, $t)
  {
    date_default_timezone_set('UTC');
    $date = date(BasicDateFormat, $t);
    $hash = hash('sha256', $canonicalRequest);
    return "SDK-HMAC-SHA256\n$date\n$hash";
  }

  /**
   * Calculate the hexadecimal HMAC-SHA256 signature.
   */
  function SignStringToSign($stringToSign, $signingKey)
  {
    return hash_hmac('sha256', $stringToSign, $signingKey);
  }

  /**
   * Format the Authorization header from a signature and signed headers.
   */
  function AuthHeaderValue($signature, $accessKey, $signedHeaders)
  {
    $signedHeadersString = join(';', $signedHeaders);
    return "SDK-HMAC-SHA256 Access=$accessKey, SignedHeaders=$signedHeadersString, Signature=$signature";
  }

  /**
   * Sign a request and create a cURL handle ready for execution.
   *
   * Adds the date and Authorization headers to the request. When configured,
   * the security token is also added. If no valid X-Sdk-Date header is present,
   * the current UTC time is used.
   *
   * @param Request $r Request to sign; its headers are updated in place.
   * @return resource|\CurlHandle|false Configured cURL handle; caller must
   *                                    execute and close it.
   */
  public function Sign($r)
  {
    date_default_timezone_set('UTC');
    $date = $this->findHeader($r, HeaderXDate);
    $t = null;
    if ($date) {
      $t = date_timestamp_get(date_create_from_format(BasicDateFormat, $date));
    }
    if (!@$t) {
      $t = time();
      $r->headers[HeaderXDate] = date(BasicDateFormat, $t);
    }
    $queryString = $this->CanonicalQueryString($r);
    if ($queryString != '') {
      $queryString = '?' . $queryString;
    }
    $signedHeaders = $this->SignedHeaders($r);
    $canonicalRequest = $this->CanonicalRequest($r, $signedHeaders);
    $stringToSign = $this->StringToSign($canonicalRequest, $t);
    $signature = $this->SignStringToSign($stringToSign, $this->Secret);
    $authValue = $this->AuthHeaderValue($signature, $this->Key, $signedHeaders);
    $r->headers[HeaderAuthorization] = $authValue;

    if ($this->SecurityToken != '') {
      $r->headers[HXSecurityToken] = $this->SecurityToken;
    }

    $curl = curl_init();
    $uri = str_replace(array('%2F'), array('/'), rawurlencode($r->uri));
    $url = $r->scheme . '://' . $r->host . $uri . $queryString;
    $headers = $this->curlHeaders($r);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $r->method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $r->body);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_NOBODY, FALSE);
    return $curl;
  }
}
?>