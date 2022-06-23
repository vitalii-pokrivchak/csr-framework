<?php

namespace Csr\Framework\Http;

use Csr\Framework\Template\TemplateProvider;
use Exception;

abstract class HttpController extends Controller
{
    protected ?TemplateProvider $template;

    /**
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     * @param TemplateProvider|null $template
     */
    public function __construct(Request $request, Response $response, ?TemplateProvider $template)
    {
        $this->template = $template;
        parent::__construct($request, $response);
    }

    /**
     * Respond with template
     *
     * @param string $view
     * @param array $data
     * @param int $code
     *
     * @return self
     */
    protected function view(string $view, array $data = [], int $code = StatusCode::OK): self
    {
        $this->response->status($code);
        $this->template->render($view, $data);
        return $this;
    }

    /**
     * Respond with file
     *
     * @param string $path
     * @param string $filename
     * @param int $code
     *
     * @return self
     */
    public function file(string $path, string $filename, int $code = StatusCode::OK): self
    {
        try {
            $this->response->status($code);
            if (file_exists($path) && !is_dir($path) && $filename != '') {
                $contentType = mime_content_type($path);
                $filesize = filesize($path);
                $this->response->contentType($contentType);
                $this->response->header("Content-Length: $filesize");
                $this->response->header("Content-Disposition: attachment; filename=$filename");
                readfile($path);
            } else {
                $this->response->status(StatusCode::NOT_FOUND);
            }
        } catch (Exception $e) {
            $this->response->status(StatusCode::SERVER_ERROR);
        } finally {
            return $this;
        }
    }
}
