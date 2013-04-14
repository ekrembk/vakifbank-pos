<?php
/**
 * VAKIFBANK Sanal Pos
 *
 * Testler
 *
 * @author     Ekrem Büyükkaya <ebuyukkaya@gmail.com> <ekrembk.com> <@ekrembk>
 * @package    php-vakifbank-pos
 * @subpackage Test
 */

require 'vakifbank-pos.php';
use Vakifbank_Pos as VP;

$pos = new VP\Vakifbank_Pos;

// Ayarlar
$pos->kullanici     = '';
$pos->sifre         = '';
$pos->uye_isyeri_no = '';
$pos->pos_no        = '';
$pos->xcip          = '';

// Sonuç
$sonuc = $pos->pro( 12.50, '00', '0000000000000000', '123', '1213' );

// Sonuçla ilgili bilgileri ekrana yazdır
header( 'Content-type: text/html;charset=utf-8' );

echo 'Sonuç  : ' . ( $sonuc ? 'Başarılı' : 'Başarısız' );
echo 'Döngü  : ' . $pos->dongu;
echo 'RAW    : ' . $pos->raw;
?>