<?php

namespace Csr\Framework\Http;

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
        $this->finish($value, StatusCode::CREATED);
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
        $this->finish($response, StatusCode::NOT_FOUND);
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
        $this->finish($response, StatusCode::BAD);
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
        $this->finish($response, StatusCode::SERVER_ERROR);
        return $this;
    }

    public function finish($value, $status = StatusCode::OK)
    {
        $this->response->contentType(ContentType::JSON)->status($status);
        echo json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
