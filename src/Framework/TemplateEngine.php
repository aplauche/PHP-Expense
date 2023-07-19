<?php

declare(strict_types=1);


namespace Framework;


class TemplateEngine
{
  public function __construct(private string $basePath)
  {
  }


  public function render(string $template, array $data = [])
  {
    extract($data, EXTR_SKIP); // creates vars for each item in associative array
    // SKIP means it wont overwrite existing vars

    // Output buffer is optional - allows the controller to manipulate content rendered before sending to browser
    ob_start();

    include "{$this->basePath}/{$template}";

    $output = ob_get_contents();

    ob_end_clean();

    return $output;
  }
}
