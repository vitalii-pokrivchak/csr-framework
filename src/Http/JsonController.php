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
        $this->response->status(StatusCode::OK)->contentType(ContentType::JSON);
        echo json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
        $this->response->status(StatusCode::CREATED)->contentType(ContentType::JSON);
        echo json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
        $this->response->status(StatusCode::NOT_FOUND)->contentType(ContentType::JSON);
        $response = ['status' => 404];
        $response['message'] = $message == '' ? 'Not Found' : $message;
        echo json_encode($response);
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
        $this->response->status(StatusCode::BAD)->contentType(ContentType::JSON);
        $response = ['status' => 400];
        $response['message'] = $message == '' ? 'Bad Request' : $message;
        echo json_encode($response);
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
        $this->response->status(StatusCode::SERVER_ERROR)->contentType(ContentType::JSON);
        $response = ['status' => 500];
        $response['message'] = $message == '' ? 'Internal Server Error' : $message;
        echo json_encode($response);
        return $this;
    }
}
