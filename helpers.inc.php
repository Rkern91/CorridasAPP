<?php
  /**
   * Retorna true se for um valor valido,
   * utilizado para verificar campos enviados por formulários
   *
   * @param $value
   * @return bool
   */
  function hasValue($value) : bool
  {
    return ($value !== '' && $value !== null && $value !== false);
  }
  
  /**
   * Define a primeira opção do campo select como vazio.
   *
   * @param $arrOptions
   */
  function setFirstEmpty(&$arrOptions)
  {
    array_unshift($arrOptions, "<option value=\"\"></option>");
  }
  
  /**
   * Recebe um array ou objeto como parâmetro, imprimindo
   * o valor formatado (print_r com tags <pre>).
   *
   * @param $value
   */
  function out($value)
  {
    if (PHP_SAPI != 'cli') echo "<pre style='text-align: left'>";
    print_r($value);
    if (PHP_SAPI != 'cli') echo "</pre>";
  }