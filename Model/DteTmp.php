<?php

/**
 * LibreDTE
 * Copyright (C) SASCO SpA (https://sasco.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General Affero de GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General Affero de GNU para
 * obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

// namespace del modelo
namespace website\Dte;

/**
 * Clase para mapear la tabla dte_tmp de la base de datos
 * Comentario de la tabla:
 * Esta clase permite trabajar sobre un registro de la tabla dte_tmp
 * @author SowerPHP Code Generator
 * @version 2015-09-22 01:01:43
 */
class Model_DteTmp extends \Model_App
{

    // Datos para la conexión a la base de datos
    protected $_database = 'default'; ///< Base de datos del modelo
    protected $_table = 'dte_tmp'; ///< Tabla del modelo

    // Atributos de la clase (columnas en la base de datos)
    public $emisor; ///< integer(32) NOT NULL DEFAULT '' PK FK:contribuyente.rut
    public $receptor; ///< integer(32) NOT NULL DEFAULT '' PK FK:contribuyente.rut
    public $dte; ///< smallint(16) NOT NULL DEFAULT '' PK FK:dte_tipo.codigo
    public $codigo; ///< character(32) NOT NULL DEFAULT '' PK
    public $fecha; ///< date() NOT NULL DEFAULT ''
    public $total; ///< integer(32) NOT NULL DEFAULT ''
    public $datos; ///< text() NOT NULL DEFAULT ''
    public $sucursal_sii; ///< integer(32) NULL DEFAULT ''
    public $usuario; ///< integer(32) NULL DEFAULT ''

    // Información de las columnas de la tabla en la base de datos
    public static $columnsInfo = array(
        'emisor' => array(
            'name'      => 'Emisor',
            'comment'   => '',
            'type'      => 'integer',
            'length'    => 32,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => true,
            'fk'        => array('table' => 'contribuyente', 'column' => 'rut')
        ),
        'receptor' => array(
            'name'      => 'Receptor',
            'comment'   => '',
            'type'      => 'integer',
            'length'    => 32,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => true,
            'fk'        => array('table' => 'contribuyente', 'column' => 'rut')
        ),
        'dte' => array(
            'name'      => 'Dte',
            'comment'   => '',
            'type'      => 'smallint',
            'length'    => 16,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => true,
            'fk'        => array('table' => 'dte_tipo', 'column' => 'codigo')
        ),
        'codigo' => array(
            'name'      => 'Codigo',
            'comment'   => '',
            'type'      => 'character',
            'length'    => 32,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => true,
            'fk'        => null
        ),
        'fecha' => array(
            'name'      => 'Fecha',
            'comment'   => '',
            'type'      => 'date',
            'length'    => null,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => false,
            'fk'        => null
        ),
        'total' => array(
            'name'      => 'Total',
            'comment'   => '',
            'type'      => 'integer',
            'length'    => 32,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => false,
            'fk'        => null
        ),
        'datos' => array(
            'name'      => 'Datos',
            'comment'   => '',
            'type'      => 'text',
            'length'    => null,
            'null'      => false,
            'default'   => '',
            'auto'      => false,
            'pk'        => false,
            'fk'        => null
        ),
        'sucursal_sii' => array(
            'name'      => 'Sucursal SII',
            'comment'   => '',
            'type'      => 'integer',
            'length'    => 32,
            'null'      => true,
            'default'   => '',
            'auto'      => false,
            'pk'        => false,
            'fk'        => null
        ),
        'usuario' => array(
            'name'      => 'Usuario',
            'comment'   => '',
            'type'      => 'integer',
            'length'    => 32,
            'null'      => true,
            'default'   => '',
            'auto'      => false,
            'pk'        => false,
            'fk'        => array('table' => 'usuario', 'column' => 'id')
        ),

    );

    // Comentario de la tabla en la base de datos
    public static $tableComment = '';

    public static $fkNamespace = array(
        'Model_Contribuyente' => 'website\Dte',
        'Model_DteTipo' => 'website\Dte'
    ); ///< Namespaces que utiliza esta clase

    private $Receptor; ///< Caché para el receptor
    private $cache_datos; ///< Caché para los datos del documento

    /**
     * Método que genera el XML de EnvioDTE a partir de los datos ya
     * normalizados de un DTE temporal
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2018-11-06
     */
    public function getEnvioDte($folio = 0, \sasco\LibreDTE\Sii\Folios $Folios = null, \sasco\LibreDTE\FirmaElectronica $Firma = null, $RutReceptor = null, $fecha_emision = null)
    {
        $dte = json_decode($this->datos, true);
        if (!$dte) {
            return false;
        }
        $dte['Encabezado']['IdDoc']['Folio'] = $folio;
        if ($fecha_emision) {
            $dte['Encabezado']['IdDoc']['FchEmis'] = $fecha_emision;
        }
        $Dte = new \sasco\LibreDTE\Sii\Dte($dte, false);
        if ($Folios and !$Dte->timbrar($Folios)) {
            return false;
        }
        if ($Firma and !$Dte->firmar($Firma)) {
            return false;
        }
        $EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
        $EnvioDte->agregar($Dte);
        if ($Firma) {
            $EnvioDte->setFirma($Firma);
        }
        $Emisor = $this->getEmisor();
        $EnvioDte->setCaratula([
            'RutEnvia' => $Firma ? $Firma->getID() : false,
            'RutReceptor' => $RutReceptor ? $RutReceptor : $Dte->getReceptor(),
            'FchResol' => $Emisor->config_ambiente_en_certificacion ? $Emisor->config_ambiente_certificacion_fecha : $Emisor->config_ambiente_produccion_fecha,
            'NroResol' => $Emisor->config_ambiente_en_certificacion ? 0 : $Emisor->config_ambiente_produccion_numero,
        ]);
        return $EnvioDte;
    }

    /**
     * Método que entrega el objeto de receptor
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-08-05
     */
    public function getReceptor()
    {
        if ($this->Receptor === null) {
            $this->Receptor = (new Model_Contribuyentes())->get($this->receptor);
            if (in_array($this->dte, [110, 111, 112])) {
                $datos = json_decode($this->datos, true)['Encabezado']['Receptor'];
                $this->Receptor->razon_social = $datos['RznSocRecep'];
                $this->Receptor->direccion = $datos['DirRecep'];
                $this->Receptor->comuna = null;
            }
        }
        return $this->Receptor;
    }

    /**
     * Método que entrega el objeto del tipo de dte
     * @deprecated Ya que en DteEmitido este método retorna \sasco\LibreDTE\Sii\Dte
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-02-04
     */
    public function getDte()
    {
        return $this->getTipo();
    }

    /**
     * Método que entrega el objeto del tipo de dte
     * @return \website\Dte\Admin\Mantenedores\Model_DteTipo
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-02-04
     */
    public function getTipo()
    {
        return (new \website\Dte\Admin\Mantenedores\Model_DteTipos())->get($this->dte);
    }

    /**
     * Método que entrega el objeto del emisor
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-01-02
     */
    public function getEmisor()
    {
        return (new \website\Dte\Model_Contribuyentes())->get($this->emisor);
    }

    /**
     * Método que entrega el folio del documento temporal
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-06-13
     */
    public function getFolio()
    {
        return $this->dte.'-'.strtoupper(substr($this->codigo, 0, 7));
    }

    /**
     * Método que crea el DTE real asociado al DTE temporal
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2018-12-26
     */
    public function generar($user_id = null, $fecha_emision = null)
    {
        $Emisor = $this->getEmisor();
        if (!$user_id) {
            $user_id = $Emisor->usuario;
        }
        // obtener firma electrónica
        $Firma = $Emisor->getFirma($user_id);
        if (!$Firma) {
            throw new \Exception('No hay firma electrónica asociada a la empresa (o bien no se pudo cargar), debe agregar su firma antes de generar DTE', 506);
        }
        // solicitar folio
        $datos_dte = $this->getDatos();
        $folio_dte = !empty($datos_dte['Encabezado']['IdDoc']['Folio']) ? (int)$datos_dte['Encabezado']['IdDoc']['Folio'] : 0;
        if ($folio_dte) {
            $Usuario = new \sowerphp\app\Sistema\Usuarios\Model_Usuario($user_id);
            if (!$Emisor->puedeAsignarFolio($Usuario)) {
                $folio_dte = 0;
            }
        }
        $FolioInfo = $Emisor->getFolio($this->dte, $folio_dte);
        if (!$FolioInfo) {
            throw new \Exception('No fue posible obtener un folio para el DTE de tipo '.$this->dte, 508);
        }
        // si el CAF no está vigente se alerta al usuario
        if (!$FolioInfo->Caf->vigente()) {
            throw new \Exception('Se obtuvo el CAF para el folio T'.$FolioInfo->DteFolio->dte.'F'.$FolioInfo->folio.', sin embargo el CAF no está vigente. Debe anular los folios del CAF vencido y solicitar uno nuevo.', 508);
        }
        // si quedan pocos folios timbrar o alertar según corresponda
        if ($FolioInfo->DteFolio->disponibles<=$FolioInfo->DteFolio->alerta) {
            $timbrado = false;
            // timbrar automáticmente
            if ($Emisor->config_sii_timbraje_automatico==1) {
                try {
                    $FolioInfo->DteFolio->timbrar($FolioInfo->DteFolio->alerta*$Emisor->config_sii_timbraje_multiplicador);
                    $timbrado = true;
                } catch (\Exception $e) {
                }
            }
            // notificar al usuario administrador
            if (!$timbrado and !$FolioInfo->DteFolio->alertado) {
                $asunto = 'Alerta de folios tipo '.$FolioInfo->DteFolio->dte;
                $msg = 'Se ha alcanzado el límite de folios del tipo de DTE '.$FolioInfo->DteFolio->dte.' para el contribuyente '.$Emisor->razon_social.', quedan '.$FolioInfo->DteFolio->disponibles.'. Por favor, solicite un nuevo archivo CAF y súbalo a LibreDTE en '.\sowerphp\core\Configure::read('app.url').'/dte/admin/dte_folios';
                if ($Emisor->notificar($asunto, $msg)) {
                    $FolioInfo->DteFolio->alertado = 1;
                    $FolioInfo->DteFolio->save();
                }
            }
        }
        // armar xml a partir del DTE temporal
        $EnvioDte = $this->getEnvioDte($FolioInfo->folio, $FolioInfo->Caf, $Firma, null, $fecha_emision);
        if (!$EnvioDte) {
            throw new \Exception('No fue posible generar el objeto del EnvioDTE. Folio '.$FolioInfo->folio.' quedará sin usar.<br/>'.implode('<br/>', \sasco\LibreDTE\Log::readAll()), 510);
        }
        $xml = $EnvioDte->generar();
        if (!$xml or !$EnvioDte->schemaValidate()) {
            throw new \Exception('No fue posible generar el XML del EnvioDTE. Folio '.$FolioInfo->folio.' quedará sin usar.<br/>'.implode('<br/>', \sasco\LibreDTE\Log::readAll()), 510);
        }
        // guardar DTE
        $r = $EnvioDte->getDocumentos()[0]->getResumen();
        $DteEmitido = new Model_DteEmitido($Emisor->rut, $r['TpoDoc'], $r['NroDoc'], (int)$Emisor->config_ambiente_en_certificacion);
        if ($DteEmitido->exists()) {
            throw new \Exception('Ya existe un DTE del tipo '.$r['TpoDoc'].' y folio '.$r['NroDoc'].' emitido', 409);
        }
        $cols = ['tasa'=>'TasaImp', 'fecha'=>'FchDoc', 'sucursal_sii'=>'CdgSIISucur', 'receptor'=>'RUTDoc', 'exento'=>'MntExe', 'neto'=>'MntNeto', 'iva'=>'MntIVA', 'total'=>'MntTotal'];
        foreach ($cols as $attr => $col) {
            if ($r[$col]!==false) {
                $DteEmitido->$attr = $r[$col];
            }
        }
        $DteEmitido->receptor = substr($DteEmitido->receptor, 0, -2);
        $DteEmitido->xml = base64_encode($xml);
        $DteEmitido->usuario = $user_id;
        if (in_array($DteEmitido->dte, [110, 111, 112])) {
            $DteEmitido->total = $DteEmitido->exento = $this->total;
        }
        $DteEmitido->anulado = 0;
        $DteEmitido->iva_fuera_plazo = 0;
        $DteEmitido->save();
        // guardar referencias si existen
        $datos = json_decode($this->datos, true);
        $nc_referencia_boleta = false;
        if (!empty($datos['Referencia'])) {
            if (!isset($datos['Referencia'][0])) {
                $datos['Referencia'] = [$datos['Referencia']];
            }
            foreach ($datos['Referencia'] as $referencia) {
                if (!empty($referencia['TpoDocRef']) and is_numeric($referencia['TpoDocRef']) and $referencia['TpoDocRef']<200) {
                    // guardar referencia
                    $DteReferencia = new Model_DteReferencia();
                    $DteReferencia->emisor = $DteEmitido->emisor;
                    $DteReferencia->dte = $DteEmitido->dte;
                    $DteReferencia->folio = $DteEmitido->folio;
                    $DteReferencia->certificacion = $DteEmitido->certificacion;
                    $DteReferencia->referencia_dte = $referencia['TpoDocRef'];
                    $DteReferencia->referencia_folio = $referencia['FolioRef'];
                    $DteReferencia->codigo = !empty($referencia['CodRef']) ? $referencia['CodRef'] : null;
                    $DteReferencia->razon = !empty($referencia['RazonRef']) ? $referencia['RazonRef'] : null;
                    $DteReferencia->save();
                    // si es nota de crédito asociada a boleta se recuerda por si se debe invalidar RCOF
                    if ($DteEmitido->dte==61 and in_array($referencia['TpoDocRef'], [39, 41])) {
                        $nc_referencia_boleta = true;
                    }
                }
            }
        }
        // guardar pagos programados si existen
        $MntPagos = $DteEmitido->getPagosProgramados();
        if (!empty($MntPagos)) {
            foreach ($MntPagos as $pago) {
                $Cobranza = new \website\Dte\Cobranzas\Model_Cobranza();
                $Cobranza->emisor = $DteEmitido->emisor;
                $Cobranza->dte = $DteEmitido->dte;
                $Cobranza->folio = $DteEmitido->folio;
                $Cobranza->certificacion = $DteEmitido->certificacion;
                $Cobranza->fecha = $pago['FchPago'];
                $Cobranza->monto = $pago['MntPago'];
                $Cobranza->glosa = !empty($pago['GlosaPagos']) ? $pago['GlosaPagos'] : null;
                $Cobranza->save();
            }
        }
        // invalidar RCOF si es una boleta o referencia de boleta y la fecha de
        // emisión es anterior al día actual
        if ($DteEmitido->fecha < date('Y-m-d')) {
            if (in_array($DteEmitido->dte, [39, 41]) or $nc_referencia_boleta) {
                $DteBoletaConsumo = new Model_DteBoletaConsumo($DteEmitido->emisor, $DteEmitido->fecha, (int)$DteEmitido->certificacion);
                if ($DteBoletaConsumo->track_id) {
                    $DteBoletaConsumo->track_id = null;
                    $DteBoletaConsumo->revision_estado = null;
                    $DteBoletaConsumo->revision_detalle = null;
                    $DteBoletaConsumo->save();
                }
            }
        }
        // enviar al SII
        try {
            $DteEmitido->enviar($user_id);
        } catch (\Exception $e) {
        }
        // ejecutar trigger asociado a la generación del DTE real
        \sowerphp\core\Trigger::run('dte_documento_generado', $this, $DteEmitido);
        // eliminar DTE temporal
        $this->delete();
        // entregar DTE emitido
        return $DteEmitido;
    }

    /**
     * Método que realiza verificaciones a campos antes de guardar
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2017-10-16
     */
    public function save()
    {
        // trigger al guardar el DTE temporal
        \sowerphp\core\Trigger::run('dte_dte_tmp_guardar', $this);
        // guardar DTE temporal
        return parent::save();
    }

    /**
     * Método que borra el DTE temporal y su cobro asociado si existe
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-12-16
     */
    public function delete($borrarCobro = true)
    {
        $this->db->beginTransaction();
        if ($borrarCobro and $this->getEmisor()->config_pagos_habilitado) {
            $Cobro = $this->getCobro(false);
            if ($Cobro->exists() and !$Cobro->pagado) {
                if (!$Cobro->delete(false)) {
                    $this->db->rollback();
                    return false;
                }
            }
        }
        if (!parent::delete()) {
            $this->db->rollback();
            return false;
        }
        $this->db->commit();
        return true;
    }

    /**
     * Método que entrega el listado de correos a los que se podría enviar el documento
     * temporal (correo receptor, correo del dte y contacto comercial)
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-02-12
     */
    public function getEmails()
    {
        $origen = (int)$this->getEmisor()->config_emision_origen_email;
        $emails = [];
        $datos = $this->getDatos();
        if (!in_array($this->dte, [39, 41])) {
            if (in_array($origen, [0, 1, 2]) and !empty($datos['Encabezado']['Receptor']['CorreoRecep'])) {
                $emails['Documento'] = strtolower($datos['Encabezado']['Receptor']['CorreoRecep']);
            }
        } else if (!empty($datos['Referencia'])) {
            if (!isset($datos['Referencia'][0])) {
                $datos['Referencia'] = [$datos['Referencia']];
            }
            foreach ($datos['Referencia'] as $r) {
                if (strpos($r['RazonRef'], 'Email receptor:')===0) {
                    $aux = explode('Email receptor:', $r['RazonRef']);
                    if (!empty($aux[1])) {
                        $email_dte = strtolower(trim($aux[1]));
                        if (in_array($origen, [0, 1, 2]) and $email_dte) {
                            $emails['Documento'] = $email_dte;
                        }
                    }
                    break;
                }
            }
        }
        if (in_array($origen, [0]) and $this->getReceptor()->email and !in_array($this->getReceptor()->email, $emails)) {
            $emails['Compartido LibreDTE'] = strtolower($this->getReceptor()->email);
        }
        if (in_array($origen, [0, 1]) and $this->getReceptor()->usuario and $this->getReceptor()->getUsuario()->email and !in_array(strtolower($this->getReceptor()->getUsuario()->email), $emails)) {
            $emails['Usuario LibreDTE'] = strtolower($this->getReceptor()->getUsuario()->email);
        }
        if ($this->emisor==\sowerphp\core\Configure::read('libredte.proveedor.rut')) {
            if ($this->getReceptor()->config_app_contacto_comercial) {
                $i = 1;
                foreach($this->getReceptor()->config_app_contacto_comercial as $contacto) {
                    if (!in_array(strtolower($contacto->email), $emails)) {
                        $emails['Comercial LibreDTE #'.$i++] = strtolower($contacto->email);
                    }
                }
            }
        }
        $emails_trigger = \sowerphp\core\Trigger::run('dte_dte_tmp_emails', $this, $emails);
        return $emails_trigger ? $emails_trigger : $emails;
    }

    /**
     * Método que envía el DTE temporal por correo electrónico
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2018-06-06
     */
    public function email($to = null, $subject = null, $msg = null, $cotizacion = true)
    {
        $Request = new \sowerphp\core\Network_Request();
        // variables por defecto
        if (!$to) {
            $to = $this->getEmails();
        }
        if (!$to) {
            throw new \Exception('No hay correo a quien enviar el DTE');
        }
        if (!is_array($to)) {
            $to = [$to];
        }
        if (!$subject) {
            $subject = 'Documento N° '.$this->getFolio().' de '.$this->getEmisor()->getNombre().' ('.$this->getEmisor()->getRUT().')';
        }
        // armar cuerpo del correo
        $msg_html = $this->getEmisor()->getEmailFromTemplate('dte', $this, $msg);
        if (!$msg) {
            $msg .= 'Se adjunta documento N° '.$this->getFolio().' del día '.\sowerphp\general\Utility_Date::format($this->fecha).' por un monto total de $'.num($this->total).'.-'."\n\n";
            $links = $this->getLinks();
            if (!empty($links['pagar'])) {
                $msg .= 'Enlace pago en línea: '.$links['pagar']."\n\n";
            }
        }
        if ($msg_html) {
            $msg = ['text' => $msg, 'html' => $msg_html];
        }
        // crear email
        $email = $this->getEmisor()->getEmailSmtp();
        $email->to($to);
        if ($this->getEmisor()->config_pagos_email or $this->getEmisor()->email) {
            $email->replyTo($this->getEmisor()->config_pagos_email ? $this->getEmisor()->config_pagos_email : $this->getEmisor()->email);
        }
        $email->subject($subject);
        // adjuntar PDF
        $rest = new \sowerphp\core\Network_Http_Rest();
        $rest->setAuth($this->getEmisor()->getUsuario()->hash);
        if ($cotizacion) {
            $response = $rest->get($Request->url.'/dte/dte_tmps/cotizacion/'.$this->receptor.'/'.$this->dte.'/'.$this->codigo.'/'.$this->emisor);
        } else {
            $response = $rest->get($Request->url.'/api/dte/dte_tmps/pdf/'.$this->receptor.'/'.$this->dte.'/'.$this->codigo.'/'.$this->emisor);
        }
        if ($response['status']['code']!=200) {
            throw new \Exception($response['body']);
        }
        $email->attach([
            'data' => $response['body'],
            'name' => ($cotizacion?'cotizacion':'dte_tmp').'_'.$this->getEmisor()->getRUT().'_'.$this->getFolio().'.pdf',
            'type' => 'application/pdf',
        ]);
        // enviar email
        $status = $email->send($msg);
        if ($status===true) {
            return true;
        } else {
            throw new \Exception(
                'No fue posible enviar el email: '.$status['message']
            );
        }
    }

    /**
     * Método que entrega el arreglo con los datos del documento
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-12-14
     */
    public function getDatos()
    {
        if (!isset($this->cache_datos)) {
            $this->cache_datos = json_decode($this->datos, true);
        }
        return $this->cache_datos;
    }

    /**
     * Método que entrega el cobro asociado al DTE temporal
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-12-16
     */
    public function getCobro($crearSiNoExiste = true)
    {
        return (new \libredte\oficial\Pagos\Model_Cobro())->setDocumento($this, $crearSiNoExiste);
    }

    /**
     * Método que entrega el vencimiento del documento si es que existe
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2016-12-15
     */
    public function getVencimiento()
    {
        $datos = $this->getDatos();
        return !empty($datos['Encabezado']['IdDoc']['FchVenc']) ? $datos['Encabezado']['IdDoc']['FchVenc'] : null;
    }

    /**
     * Método que entrega el detalle del DTE
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2017-07-20
     */
    public function getDetalle()
    {
        $Detalle = $this->getDatos()['Detalle'];
        return isset($Detalle[0]) ? $Detalle : [$Detalle];
    }

    /**
     * Método que entrega los enlaces públicos del documento
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2018-06-06
     */
    public function getLinks()
    {
        $Request = new \sowerphp\core\Network_Request();
        $links = [];
        $links['ver'] = $Request->url.'/dte/dte_tmps/ver/'.$this->receptor.'/'.$this->dte.'/'.$this->codigo;
        $links['pdf'] = $Request->url.'/dte/dte_tmps/cotizacion/'.$this->receptor.'/'.$this->dte.'/'.$this->codigo.'/'.$this->emisor;
        if ($this->getEmisor()->config_pagos_habilitado and $this->getTipo()->permiteCobro()) {
            $links['pagar'] = $Request->url.'/pagos/cotizaciones/pagar/'.$this->receptor.'/'.$this->dte.'/'.$this->codigo.'/'.$this->emisor;
        }
        $links_trigger = \sowerphp\core\Trigger::run('dte_dte_tmp_links', $this, $links);
        return $links_trigger ? $links_trigger : $links;
    }

}
