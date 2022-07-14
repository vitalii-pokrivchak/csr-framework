<?php

namespace Csr\Framework\Http;

use Csr\Framework\Common\Serializable;

abstract class JsonController extends Controller
{
    /**
     * Respond with status 200 OK
     *
     * @param mixed $value
     *
     * @return self
     */
    public function ok($value): self
    {
        $this->response->status(StatusCode::OK);
        $this->finish($value);
        return $this;
    }

    /**
     * Respond with status 201 Created
     *
     * @param mixed $value
     *
     * @return self
     */
    public function created($value): self
    {
        $this->response->status(StatusCode::CREATED);
        $this->finish($value);
        return $this;
    }

    /**
     * Respond with status 404 Not Found
     *
     * @param string $message
     *
     * @return self
     */
    public function notFound($message = ''): self
    {
        $response = ['status' => 404];
        $response['message'] = $message == '' ? 'Not Found' : $message;
        $this->response->status(StatusCode::NOT_FOUND);
        $this->finish($response);
        return $this;
    }

    /**
     * Response with status 400 Bad Request
     *
     * @param string $message
     *
     * @return self
     */
    public function bad($message = ''): self
    {
        $response = ['status' => 400];
        $response['message'] = $message == '' ? 'Bad Request' : $message;
        $this->response->status(StatusCode::BAD);
        $this->finish($response);
        return $this;
    }

    /**
     * Respond with status 500 Server Error
     *
     * @param string $message
     *
     * @return self
     */
    public function error($message = ''): self
    {
        $response = ['status' => 500];
        $response['message'] = $message == '' ? 'Internal Server Error' : $message;
        $this->response->status(StatusCode::SERVER_ERROR);
        $this->finish($response,);
        return $this;
    }

    public function finish($value)
    {
        if ($value instanceof Serializable) {
            $value = $value->serialize();
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if ($v instanceof Serializable) {
                    $value[$k] = $v->serialize();
                }
            }
        }

        $this->response->contentType(ContentType::JSON);
        echo json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
