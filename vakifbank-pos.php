<?php
/**
 * VAKIFBANK Sanal Pos
 *
 * @author  Ekrem Büyükkaya <ebuyukkaya@gmail.com> <ekrembk.com> <@ekrembk>
 * @package php-vakifbank-pos
 */

namespace VakifBank_Pos;

class Vakifbank_Pos {
	/**
	 * Gateway URL
	 */
	private $gateway_domain = 'subesiz.vakifbank.com.tr';
	private $gateway_path   = '/vpos724v3/';

	/**
	 * Ayarlar
	 */

	// Https kullanılsın mı?
	public $https = true;

	// İşlem türü
	public $islem = null;

	// Üye İşyeri No
	public $uye_isyeri_no = null;

	// API Kullanıcı - Şifre
	public $kullanici = null;
	public $sifre     = null;

	// POS numarası
	public $pos_no = null;

	// XCIP
	public $xcip = null;

	/**
	 * _proxy
	 *
	 * Curl ile verileri çeken fonksiyon
	 * 
	 * @param string $url
	 */
	private function _sorgu_calistir( $islem, $tutar, $taksit, $kk_no, $cvc, $skt ) {
		if(
				is_null( $this->kullanici )
				OR is_null( $this->sifre )
				OR is_null( $this->uye_isyeri_no )
				OR is_null( $this->pos_no )
				OR is_null( $this->xcip )
			)
			throw new Vakifbank_Pos_Exception( 'İşyeri ayarları yapılmadı.' );

		// Tutar
		$tutar_son = str_pad( number_format( $tutar, 2, '', '' ), 12, '0', STR_PAD_LEFT );

		// Argümanları belirle
		$ayarlar = array(
				'kullanici' => $this->kullanici,
				'sifre'     => $this->sifre,
				'islem'     => $islem,
				'uyeno'     => $this->uye_isyeri_no,
				'posno'     => $this->pos_no,
				'kkno'      => $kk_no,
				'gectar'    => $skt,
				'cvc'       => $cvc,
				'tutar'     => $tutar_son,
				'taksits'   => $taksit,
				'xcip'      => $this->xcip,
				'khip'      => '195.195.195.195', // $_SERVER['REMOTE_ADDR'],

				// Sabitler
				'islemyeri' => 'I',
				'vbref'     => '0',
				'uyeref'    => uniqid(),
				'provno'    => '123456'
			);

		// Curl ile sorguyu yap
		// URL'yi oluştur
		$this->raw = $this->_curl( $this->_url( $ayarlar ) );

		// Sonucu getir
		if( ! preg_match( '/<Kod>([^<]*)<\/Kod>/i', $this->raw, $cikti ) )
			throw new Vakifbank_Pos_Exception( 'Geçersiz sonuç.' );

		// Sonuç
		$this->dongu = $cikti[1];

		return $this->dongu == '00' 
			? true
			: false;
	}

	/**
	 * _sorgu calistir için yardımcı fonksiyonlar
	 */
	public function pro( $tutar, $taksit, $kk_no, $cvc, $skt ) {
		return $this->_sorgu_calistir( 'PRO', $tutar, $taksit, $kk_no, $cvc, $skt );
	}


	/**
	 * _curl
	 *
	 * Curl ile verileri çeken fonksiyon
	 * 
	 * @param string $url
	 */
	private function _curl( $url ) {
		// Bağlantıyı başlat
		$ch = curl_init();

		// Ayarlar
		$ayarlar = array(
				CURLOPT_URL            => $url,
				CURLOPT_HEADER         => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT        => 60,
				CURLOPT_SSL_VERIFYPEER => false
			);
		curl_setopt_array( $ch, $ayarlar );

		// Veri
		$veri = curl_exec( $ch );

		// Bağlantıyı kapat
		curl_close( $ch );

		return $veri;
	}

	/**
	 * _url
	 *
	 * API sorgusunu yapmak için parametrelerle URL'yi belirle
	 * 
	 * @param array $ayarlar
	 */
	private function _url( $ayarlar ) {
		return ( $this->https ? 'https' : 'http' )
				. '://'
				. $this->gateway_domain
				. $this->gateway_path
				. '?'
				. http_build_query( $ayarlar );
	}
}

/**
 * Vakifbank_Pos_Exception
 */
class Vakifbank_Pos_Exception extends \Exception {

}