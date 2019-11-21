# SmartNews RSS Writer

![GitHub top language](https://img.shields.io/github/languages/top/y-matsuda18/SmartNews-RSS-Writer)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/y-matsuda18/SmartNews-RSS-Writer)
![GitHub](https://img.shields.io/github/license/y-matsuda18/SmartNews-RSS-Writer)

[SmartFormat仕様書（RSS2.0準拠）](https://publishers.smartnews.com/hc/ja/articles/360010977813)に基づいてXMLを出力する。  
Outputs XML based on the [SmartFormat Specification](https://publishers.smartnews.com/hc/en-us/articles/360036526213-SmartFormat-Specification-Version-2-1-).

## 使い方 / How to use

### 必要条件 / Prerequisites

PHP5 or higher

### Sample

#### 1. インスタンス作成 / Creating an instance

```php
$obj = new SmartNewsRSSWriter();
```

#### 2. チャンネル情報の設定 / Set channel information

```php
$chInfo = array(
	'title' => 'SITE TITLE',
	'link' => 'https://example.com/',
	'description' => 'SITE DESCRIPTION',
	'pubDate' => date(DATE_RSS, 1573520400),
	'language' => 'ja',
	'copyright' => 'Example. All Rights Reserved.',
	'ttl' => 5,
	'snf:logo' => 'https://example.com/logo.png',
);
$obj->setChannelInfo($chInfo);
```

#### 3. ニュース情報の設定 / Set news item data

1件ずつ追加する場合  
Cases to add one by one

```php
$item = array(
	'title' => 'TITLE',
	'link' => 'https://link',
	'guid' => 'GUID',
	'pubDate' => date(DATE_RSS, 1573520400),
	'content:encoded' => '<p>CONTENTS</p>',
);
$obj->pushItemsData($item);

$item = array(
	'title' => 'TITLE_2',
	'link' => 'https://link',
	'guid' => 'GUID',
	'pubDate' => date(DATE_RSS, 1573434000),
	'content:encoded' => '<p>CONTENTS_2</p>',
);
$obj->pushItemsData($item);
```

まとめて設定する場合  
Case to set all at once

```php
$item1 = array(
	'title' => 'TITLE',
	'link' => 'https://link',
	'guid' => 'GUID',
	'pubDate' => date(DATE_RSS, 1573520400),
	'content:encoded' => '<p>CONTENTS</p>',
);
$item2 = array(
	'title' => 'TITLE_2',
	'link' => 'https://link',
	'guid' => 'GUID',
	'pubDate' => date(DATE_RSS, 1573434000),
	'content:encoded' => '<p>CONTENTS_2</p>',
);

$items = array($item1, $item2);

$obj->setItemsData($items);
```

#### 4. 出力 / Output

直接出力  
Direct output

```php
$obj->printSmartNewsRss();
```

XML文字列を返す場合  
Case to returning a string

```php
$obj->getXml();
```

ファイルに出力する場合  
Case to outputting to a file

```php
$obj->getXml('filename');
```

#### 出力例 / Sample output
```xml
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:snf="http://www.smartnews.be/snf"
>

  <channel>
    <title>SITE TITLE</title>
    <link>https://example.com/</link>
    <description>SITE DESCRIPTION</description>
    <pubDate>Tue, 12 Nov 2019 10:00:00 +0900</pubDate>
    <language>ja</language>
    <copyright>Example. All Rights Reserved.</copyright>
    <ttl>5</ttl>
    <snf:logo><url>https://example.com/logo.png</url></snf:logo>
    <item>
      <title><![CDATA[TITLE]]></title>
      <link>https://link</link>
      <guid>GUID</guid>
      <pubDate>Tue, 12 Nov 2019 10:00:00 +0900</pubDate>
      <content:encoded><![CDATA[<p>CONTENTS</p>]]></content:encoded>
    </item>
	<item>
      <title><![CDATA[TITLE_2]]></title>
      <link>https://link</link>
      <guid>GUID</guid>
      <pubDate>Mon, 11 Nov 2019 10:00:00 +0900</pubDate>
      <content:encoded><![CDATA[<p>CONTENTS_2</p>]]></content:encoded>
    </item>
  </channel>
</rss>
```
