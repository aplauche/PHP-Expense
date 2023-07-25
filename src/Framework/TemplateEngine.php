<?php

declare(strict_types=1);


namespace Framework;


class TemplateEngine
{

  private array $globalTemplateData = [];

  public function __construct(private string $basePath)
  {
  }


  public function render(string $template, array $data = [])
  {
    extract($data, EXTR_SKIP); // creates vars for each item in associative array
    // SKIP means it wont overwrite existing vars

    // Overwrite missing data with globals
    extract($this->globalTemplateData, EXTR_SKIP);

    // Output buffer is optional - allows the controller to manipulate content rendered before sending to browser
    ob_start();

    include $this->resolve($template);

    $output = ob_get_contents();

    ob_end_clean();

    return $output;
  }

  public function resolve(string $path)
  {
    return "{$this->basePath}/{$path}";
  }

  public function addGlobal(string $key, mixed $value)
  {
    $this->globalTemplateData[$key] = $value;
  }
}
