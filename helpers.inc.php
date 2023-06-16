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
  
  /**
   * Formata um número para o padrao desejado.
   * @param float   $number        O numero a ser formatado.
   * @param integer $precision     A precisao decimal do numero.
   * @param string  $format_from   Formato de entrada do número. Aceito: "pt_BR" | "sys" | "us". Default: "pt_BR".
   * @param string  $format_to     Formato de saída do número. Aceito: "pt_BR" | "sys" | "us". Default: "sys".
   * @param integer $min_precision Precisão decimal mínima do número.
   * @return string
   */
  function padronizaMoeda($number, $precision, $format_from = "pt_BR", $format_to = "sys", $min_precision = null): string
  {
    $array       = array("&#34" => "");
    $format_from = strtr($format_from, $array);
    $format_to   = strtr($format_to, $array);
    
    if (!strlen($number))
      return "";
    
    switch ($format_from)
    {
      case "us":
        $number = strtr($number, array("," => ""));
        break;
      
      case "sys":
        $number = strtr($number, array("," => ""));
        break;
      
      case "pt_BR":
      default:
        $number = strtr($number, array("." => ""));
        $number = strtr($number, array("," => "."));
        break;
    }
    
    switch ($format_to)
    {
      case "us":
        $number = number_format((float)$number, $precision, ".", ",");
        break;
      
      case "sys":
        $number = number_format((float)$number, $precision, ".", "");
        break;
      
      case "pt_BR":
      default:
        $number = number_format((float)$number, $precision, ",", ".");
        break;
    }
    
    /**
     * Will trim off the "0"s at the right of the number.
     * Sample:
     *   $precision = 4;
     *   $min_precision = 2;
     *
     *   input: 1,23456, output: 1,2346
     *   input: 1,2345,  output: 1,2345
     *   input: 1,2340,  output: 1,234
     *   input: 1,2300,  output: 1,23
     *   input: 1,2000,  output: 1,20
     */
    if (is_numeric($min_precision) && $min_precision >= 0 && $precision > $min_precision)
    {
      while ($precision > $min_precision)
      {
        $min_precision++;
        
        if (substr($number, -1) == "0")
          $number = substr($number, 0, -1);
        
        if (($format_to == "pt_BR" && substr($number, -1) == ",") || ($format_to != "pt_BR" && substr($number, -1) == "."))
          $number = substr($number, 0, -1);
      }
    }
    
    return $number;
  }
  
  function padronizaFone($number, $format_from = "pt_BR", $format_to = "sys", $add_click_to_call = false): string
  {
    $number = trim($number);
    
    if (strpos($number, "*") !== false)
      return $number;
    
    $array       = array("&#34" => "");
    $format_from = strtr($format_from, $array);
    $format_to   = strtr($format_to, $array);
    
    unset($array);
    
    switch ($format_from)
    {
      case "sys":
        break;
      
      case"pt_BR":
      default:
        $number = strtr($number, array(" " => ""));
        $number = strtr($number, array("(" => ""));
        $number = strtr($number, array(")" => ""));
        $number = strtr($number, array("-" => ""));
        break;
    }
    
    switch ($format_to)
    {
      case "sys":
        break;
      
      case "pt_BR":
        
        for($i=0; $i<strlen($number); $i++)
          $array[] = substr($number, $i, 1);
        
        $number = "";
        
        //0800
        if ($array[0] == 0)
        {
          for($i=0; $i<sizeof($array); $i++)
          {
            if ($i == 4)
            {
              $array_tmp[] = "-";
              $array_tmp[] = $array[$i];
            }
            else
              $array_tmp[] = $array[$i];
          }
        }
        else
        {
          switch (sizeof($array))
          {
            case 10:
              $separator = 6;
              break;
            
            case 11:
            default:
              $separator = 7;
              break;
          }
          
          for($i=0; $i<sizeof($array); $i++)
          {
            switch ($i)
            {
              case 0:
                $array_tmp[] = "(";
                $array_tmp[] = $array[$i];
                break;
              
              case 2:
                $array_tmp[] = ")";
                $array_tmp[] = $array[$i];
                break;
              
              case $separator:
                $array_tmp[] = "-";
                $array_tmp[] = $array[$i];
                break;
              
              default:
                $array_tmp[] = $array[$i];
                break;
            }
          }
        }
        
        for($i=0; $i<sizeof($array_tmp); $i++)
          $number .= $array_tmp[$i];
        
        break;
    }
    
    //Formata o número de telefone com um link para clicar e chamar
    if ($add_click_to_call)
      $number = "<a href='tel:$number'>$number</a>";
    
    return $number;
  }