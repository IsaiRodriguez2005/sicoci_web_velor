<?php

class NumeroALetras
{
    private $unidades = [
        1 => "uno", 2 => "dos", 3 => "tres", 4 => "cuatro", 5 => "cinco",
        6 => "seis", 7 => "siete", 8 => "ocho", 9 => "nueve", 10 => "diez",
        11 => "once", 12 => "doce", 13 => "trece", 14 => "catorce", 15 => "quince",
        16 => "dieciséis", 17 => "diecisiete", 18 => "dieciocho", 19 => "diecinueve", 20 => "veinte"
    ];

    private $decenas = [
        2 => "veinti", 3 => "treinta", 4 => "cuarenta", 5 => "cincuenta",
        6 => "sesenta", 7 => "setenta", 8 => "ochenta", 9 => "noventa"
    ];

    private $centenas = [
        1 => "ciento", 2 => "doscientos", 3 => "trescientos", 4 => "cuatrocientos",
        5 => "quinientos", 6 => "seiscientos", 7 => "setecientos", 8 => "ochocientos", 9 => "novecientos"
    ];

    private $grupos = [
        3 => "mil", 6 => "millón", 9 => "mil millones", 12 => "billón"
    ];

    public function convertir($numero, $moneda = "PESOS", $region = "MX", $femenino = false, $decimales = true)
    {
        $partes = explode('.', number_format($numero, 2, '.', ''));
        $entero = (int)$partes[0];
        $decimal = $partes[1];

        $textoEntero = $this->convertirEntero($entero, $femenino);
        $textoDecimal = $this->convertirDecimal($decimal);

        return sprintf(
            '<span style="text-transform: uppercase;">%s %s</span> %s/100 %s',
            ucfirst($textoEntero),
            $moneda,
            $textoDecimal,
            $region
        );
    }

    private function convertirEntero($numero, $femenino)
    {
        if ($numero === 0) return "cero";

        $texto = "";
        $grupos = str_split(str_pad($numero, ceil(strlen($numero) / 3) * 3, "0", STR_PAD_LEFT), 3);

        foreach ($grupos as $i => $grupo) {
            $pos = count($grupos) - $i - 1;
            $texto .= $this->convertirGrupo($grupo, $pos, $femenino);
        }

        return trim($texto);
    }

    private function convertirGrupo($grupo, $pos, $femenino)
    {
        $centenas = (int)$grupo[0];
        $decenas = (int)$grupo[1];
        $unidades = (int)$grupo[2];

        $texto = "";

        if ($centenas > 0) {
            $texto .= " " . ($centenas === 1 && $grupo != "100" ? "ciento" : $this->centenas[$centenas]);
        }

        if ($decenas === 1) {
            $texto .= " " . $this->unidades[10 + $unidades];
        } elseif ($decenas > 1) {
            $texto .= " " . $this->decenas[$decenas];
            if ($unidades > 0) $texto .= " y " . $this->unidades[$unidades];
        } elseif ($unidades > 0) {
            $texto .= " " . ($femenino && $unidades === 1 ? "una" : $this->unidades[$unidades]);
        }

        if ($pos > 0) {
            $texto .= " " . ($grupo == "001" ? $this->grupos[$pos] : $this->grupos[$pos] . "es");
        }

        return $texto;
    }

    private function convertirDecimal($decimal)
    {
        return str_pad($decimal, 2, "0", STR_PAD_RIGHT);
    }
}
