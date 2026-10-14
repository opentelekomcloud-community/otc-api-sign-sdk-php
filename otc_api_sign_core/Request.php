<?php
namespace OTC;

/**
 * Holds the HTTP request data consumed by Signer.
 */
class Request
{
  /** @var string HTTP method, such as GET or POST. */
  public $method = '';
  /** @var string URL scheme; defaults to http when a URL is parsed. */
  public $scheme = '';
  /** @var string Request host, without scheme or path. */
  public $host = '';
  /** @var string URL-decoded request path, including its leading slash. */
  public $uri = '';
  /** @var array<string, array<int, string>> Decoded query values by key. */
  public $query = array();
  /** @var array<string, string> HTTP headers keyed by name. */
  public $headers = array();
  /** @var string Request body. */
  public $body = '';

  /**
   * Create a request from optional method, URL, headers, and body values.
   *
   * Arguments are positional in that order; omitted values keep their defaults.
   */
  function __construct()
  {
    $args = func_get_args();
    $i = count($args);
    if ($i == 0) {
      $this->construct(NULL, NULL, NULL, NULL);
    } elseif ($i == 1) {
      $this->construct($args[0], NULL, NULL, NULL);
    } elseif ($i == 2) {
      $this->construct($args[0], $args[1], NULL, NULL);
    } elseif ($i == 3) {
      $this->construct($args[0], $args[1], $args[2], NULL);
    } else {
      $this->construct($args[0], $args[1], $args[2], $args[3]);
    }
  }

  /**
   * Set request fields and parse the supplied URL into its components.
   * Repeated query keys are stored as arrays of URL-decoded values.
   *
   * @param string|null $method HTTP method.
   * @param string|null $url Request URL; URLs without a scheme use http.
   * @param array|null $headers HTTP headers keyed by name.
   * @param string|null $body Request body.
   */
  function construct($method, $url, $headers, $body)
  {
    if ($method != NULL) {
      $this->method = $method;
    }
    if ($url != NULL) {
      $spl = explode('://', $url, 2);
      $scheme = 'http';
      if (count($spl) > 1) {
        $scheme = $spl[0];
        $url = $spl[1];
      }
      $spl = explode('?', $url, 2);
      $url = $spl[0];
      $query = array();
      if (count($spl) > 1) {
        foreach (explode('&', $spl[1]) as $kv) {
          $spl = explode('=', $kv, 2);
          $key = $spl[0];
          if (count($spl) == 1) {
            $value = '';
          } else {
            $value = $spl[1];
          }
          if ($key != '') {
            $key = urldecode($key);
            $value = urldecode($value);
            if (array_key_exists($key, $query)) {
              array_push($query[$key], $value);
            } else {
              $query[$key] = array($value);
            }
          }
        }
      }
      $spl = explode('/', $url, 2);
      $host = $spl[0];
      if (count($spl) == 1) {
        $url = '/';
      } else {
        $url = '/' . $spl[1];
      }
      $this->scheme = $scheme;
      $this->host = $host;
      $this->uri = urldecode($url);
      $this->query = $query;
    }
    if ($headers != NULL) {
      $this->headers = $headers;
    }
    if ($body != NULL) {
      $this->body = $body;
    }
  }
}
?>