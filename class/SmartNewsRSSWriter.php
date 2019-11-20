<?php
/**
 * SmartNewsRSSWriter.php
 * 
 * SmartFormat仕様書（RSS2.0準拠）に基づいた形式で
 * XMLを出力する。
 * 
 * @author Yasunori Matsuda
 * @version 0.201909
 * @link https://publishers.smartnews.com/hc/ja/articles/360010977813-SmartFormat%E4%BB%95%E6%A7%98%E6%9B%B8-RSS2-0%E6%BA%96%E6%8B%A0-
 */
class SmartNewsRSSWriter
{
	/********************************
	 * プロパティ					*
	 ********************************/

	/**
	 * チャンネル情報
	 * 
	 * xmlタグ名をキーとしてチャンネル情報を格納する
	 * 
	 * @property array $channelInfo
	 */
	protected $channelInfo = array();

	/**
	 * 記事情報
	 * 
	 * xmlタグ名をキーとして記事内容を格納する
	 * 
	 * @property array $itemsData
	 */
	protected $itemsData = array();

	/**
	 * XML名前空間
	 * 
	 * 接頭辞をキーとしてURIを格納する
	 * 
	 * @property array $xmlns
	 */
	protected $xmlns = array();


	/********************************
	 * メソッド						*
	 ********************************/

	/**
	 * コンストラクタ
	 * 
	 * XML名前空間とチャンネル情報を設定する
	 * 
	 * @param array $chInfo チャンネル情報
	 * @return void
	 **/
	public function __construct(array $chInfo = array())
	{
		$this->setXmlns(array(
			'content'	=> 'http://purl.org/rss/1.0/modules/content/',
			'dc'		=> 'http://purl.org/dc/elements/1.1/',
			'media'		=> 'http://search.yahoo.com/mrss/',
			'snf'		=> 'http://www.smartnews.be/snf',
		))
			->setChannelInfo($chInfo);

		return;
	}

	/**
	 * デストラクタ
	 *
	 * 何もしない
	 **/
	public function __destruct()
	{
	}
	
	/**
	 * チャンネル情報の取得
	 * 
	 * @return array
	 **/
	public function getChannelInfo()
	{
		return $this->channelInfo;
	}

	/**
	 * 記事情報の取得
	 * 
	 * @return array
	 **/
	public function getItemsData()
	{
		return $this->itemsData;
	}

	/**
	 * xml名前空間の取得
	 * 
	 * @return array
	 **/
	public function getXmlns()
	{
		return $this->xmlns;
	}

	/**
	 * チャンネル情報のセット
	 * 
	 * @param array $info チャンネル情報
	 * @return SmartNewsRSSWriter
	 **/
	public function setChannelInfo(array $info)
	{
		$this->channelInfo = $info;
		return $this;
	}

	/**
	 * 記事情報のセット
	 * 
	 * @param array $data 記事情報
	 * @return SmartNewsRSSWriter
	 **/
	public function setItemsData(array $data)
	{
		$this->itemsData = $data;
		return $this;
	}

	/**
	 * XML名前空間のセット
	 * 
	 * @param array $xmlnsInfo XML名前空間情報
	 * @return SmartNewsRSSWriter
	 **/
	public function setXmlns(array $xmlnsInfo)
	{
		$this->xmlns = $xmlnsInfo;
		return $this;
	}

	/**
	 * 記事情報を1件追加
	 * 
	 * $itemsDataの末尾に記事情報を1件追加する
	 * 
	 * @param array $item 1件分の記事情報
	 * @return SmartNewsRSSWriter
	 **/
	public function pushItemsData(array $item)
	{
		$this->itemsData[] = $item;
		return $this;
	}

	/**
	 * XML名前空間の追加
	 * 
	 * $xmlnsにXML名前空間を1件追加する
	 * 
	 * @param string $prefix 接頭辞
	 * @param string $uri URI
	 * @return SmartNewsRSSWriter
	 **/
	public function addXmlns($prefix, $uri)
	{
		$this->xmlns[$prefix] = $uri;
		return $this;
	}

	/**
	 * XML宣言文とRSS宣言文のXML生成
	 * 
	 * @return string
	 */
	public function generateXmlPreamble()
	{
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml .= '<rss version="2.0"' . "\n";
		foreach ($this->getXmlns() as $prefix => $uri) {
			$xml .= "     xmlns:${prefix}=\"${uri}\"\n";
		}
		$xml .= ">\n\n";
		return $xml;
	}

	/**
	 * チャンネル情報のXML生成
	 * 
	 * @return string
	 */
	public function generateXmlChannelInfo()
	{
		$xml = "  <channel>\n";
		foreach ($this->getChannelInfo() as $tag => $value) {
			switch ($tag) {
				case 'snf:logo':
					$xml .= "    <${tag}><url>" . htmlspecialchars($value) . "</url></${tag}>\n";
					break;
				default:
					$xml .= "    <${tag}>" . htmlspecialchars($value) . "</${tag}>\n";
					break;
			}
		}
		return $xml;
	}

	/**
	 * 記事情報のXML生成
	 *
	 * @return string
	 */
	public function generateXmlItemsData()
	{
		$xml = '';
		foreach ($this->getItemsData() as $data) {
			$xml .= "    <item>\n";
			foreach ($data as $tag => $value) {
				if (!is_null($value)) {
					switch ($tag) {
						case 'title':
						case 'description':
						case 'content:encoded':
							$xml .= "      <${tag}><![CDATA[" . $value . "]]></${tag}>\n";
							break;
						case 'media:thumbnail':
							$xml .= "      <${tag} url=\"" . htmlspecialchars($value) . "\" />\n";
							break;
						case 'snf:video':
							// TODO
							break;
						case 'snf:advertisement':
							// TODO
							break;
						case 'snf:analytics':
							$xml .= "      <${tag}><![CDATA[\n" . $value . "\n]]></${tag}>\n";
							break;
						default:
							$xml .= "      <${tag}>" . htmlspecialchars($value) . "</${tag}>\n";
							break;
					}
				}
			}
			$xml .= "    </item>\n";
		}
		return $xml;
	}

	/**
	 * 終了タグの生成
	 * 
	 * @return string
	 */
	public function generateXmlPostamble()
	{
		$xml =  "  </channel>\n";
		$xml .= "</rss>\n";
		return $xml;
	}

	/**
	 * XML文の取得
	 * 
	 * @param string $file 書き込むファイル名
	 * @return mixed
	 */
	public function getXml($file = null)
	{
		$xml = $this->generateXmlPreamble() . $this->generateXmlChannelInfo() . $this->generateXmlItemsData() . $this->generateXmlPostamble();
		if ($file === null) {
			return $xml;
		}
		else {
			return file_put_contents($file, $xml);
		}
	}

	/**
	 * 全て出力
	 * 
	 * @return void
	 **/
	public function printSmartNewsRss()
	{
		$this->printPreamble()
			->printChannelInfo()
			->printItemsData()
			->printPostamble();
	}

	/**
	 * XML宣言文とRSS宣言文の出力
	 * 
	 * @return SmartNewsRSSWriter
	 **/
	public function printPreamble()
	{
		header("Content-type: text/xml;charset=utf-8");
		echo $this->generateXmlPreamble();
		return $this;
	}

	/**
	 * チャンネル情報の出力
	 * 
	 * @return SmartNewsRSSWriter
	 **/
	public function printChannelInfo()
	{
		echo $this->generateXmlChannelInfo();
		return $this;
	}
	
	/**
	 * 記事情報の出力
	 * 
	 * @return SmartNewsRSSWriter
	 **/
	public function printItemsData()
	{
		echo $this->generateXmlItemsData();
		return $this;
	}

	/**
	 * 終了タグの出力
	 * 
	 * @return SmartNewsRSSWriter
	 **/
	public function printPostamble()
	{
		echo $this->generateXmlPostamble();
		return $this;
	}
}
