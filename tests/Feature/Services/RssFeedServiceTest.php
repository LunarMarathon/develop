<?php

namespace Hyde\Testing\Feature\Services;

use Hyde\Framework\Hyde;
use Hyde\Framework\Services\RssFeedService;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Services\RssFeedService
 */
class RssFeedServiceTest extends TestCase
{
    // Test service instantiates the XML element
    public function test_service_instantiates_xml_element()
    {
        $service = new RssFeedService();
        $this->assertInstanceOf('SimpleXMLElement', $service->feed);
    }

    // Test XML root element is set to RSS 2.0
    public function test_xml_root_element_is_set_to_rss_2_0()
    {
        $service = new RssFeedService();
        $this->assertEquals('rss', $service->feed->getName());
        $this->assertEquals('2.0', $service->feed->attributes()->version);
    }

    // Test XML element has channel element
    public function test_xml_element_has_channel_element()
    {
        $service = new RssFeedService();
        $this->assertObjectHasAttribute('channel', $service->feed);
    }

    // Test XML channel element has required elements
    public function test_xml_channel_element_has_required_elements()
    {
        config(['hyde.name' => 'Test Blog']);
        config(['hyde.site_url' => 'https://example.com']);

        $service = new RssFeedService();
        $this->assertObjectHasAttribute('title', $service->feed->channel);
        $this->assertObjectHasAttribute('link', $service->feed->channel);
        $this->assertObjectHasAttribute('description', $service->feed->channel);

        $this->assertEquals('Test Blog', $service->feed->channel->title);
        $this->assertEquals('https://example.com', $service->feed->channel->link);
        $this->assertEquals('Test Blog RSS Feed', $service->feed->channel->description);
    }

    // Test XML channel element has additional elements
    public function test_xml_channel_element_has_additional_elements()
    {
        config(['hyde.site_url' => 'https://example.com']);

        $service = new RssFeedService();
        $this->assertObjectHasAttribute('link', $service->feed->channel);
        $this->assertEquals('https://example.com', $service->feed->channel->link);
        $this->assertEquals('https://example.com/feed.xml',
            $service->feed->channel->children('atom', true)->link->attributes()->href);

        $this->assertObjectHasAttribute('language', $service->feed->channel);
        $this->assertObjectHasAttribute('generator', $service->feed->channel);
        $this->assertObjectHasAttribute('lastBuildDate', $service->feed->channel);
    }

    // Test XML channel data can be customized
    public function test_xml_channel_data_can_be_customized()
    {
        config(['hyde.name' => 'Foo']);
        config(['hyde.site_url' => 'https://blog.foo.com/bar']);
        config(['hyde.rss_description' => 'Foo is a web log about stuff']);

        $service = new RssFeedService();
        $this->assertEquals('Foo', $service->feed->channel->title);
        $this->assertEquals('https://blog.foo.com/bar', $service->feed->channel->link);
        $this->assertEquals('Foo is a web log about stuff', $service->feed->channel->description);
    }

    // Test Markdown blog posts are added to RSS feed through autodiscovery
    public function test_markdown_blog_posts_are_added_to_rss_feed_through_autodiscovery()
    {
        file_put_contents(Hyde::path('_posts/rss.md'), <<<'MD'
            ---
            title: RSS
            author: Hyde
            date: "2022-05-19 10:15:30"
            description: RSS description
            category: test
            image: _media/rss-test.jpg
            ---
            
            # RSS Post
            
            Foo bar
            MD
        );

        config(['hyde.site_url' => 'https://example.com']);

        file_put_contents(Hyde::path('_media/rss-test.jpg'), 'statData'); // 8 bytes to test stat gets file length

        $service = new RssFeedService();
        $service->generate();
        $this->assertCount(1, $service->feed->channel->item);

        $item = $service->feed->channel->item[0];
        $this->assertEquals('RSS', $item->title);
        $this->assertEquals('RSS description', $item->description);
        $this->assertEquals('https://example.com/posts/rss.html', $item->link);
        $this->assertEquals(date(DATE_RSS, strtotime('2022-05-19T10:15:30+00:00')), $item->pubDate);
        $this->assertEquals('Hyde', $item->children('dc', true)->creator);
        $this->assertEquals('test', $item->category);

        $this->assertObjectHasAttribute('enclosure', $item);
        $this->assertEquals('https://example.com/media/rss-test.jpg', $item->enclosure->attributes()->url);
        $this->assertEquals('image/jpeg', $item->enclosure->attributes()->type);
        $this->assertEquals('8', $item->enclosure->attributes()->length);

        unlink(Hyde::path('_posts/rss.md'));
        unlink(Hyde::path('_media/rss-test.jpg'));
    }

    // Test getXML method returns XML string
    public function test_get_xml_method_returns_xml_string()
    {
        $service = new RssFeedService();
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', ($service->getXML()));
    }

    // Test generateFeed helper returns XML string
    public function test_generate_feed_helper_returns_xml_string()
    {
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', (RssFeedService::generateFeed()));
    }

    public function test_can_generate_sitemap_helper_returns_true_if_hyde_has_base_url()
    {
        config(['hyde.site_url' => 'foo']);
        $this->assertTrue(RssFeedService::canGenerateFeed());
    }

    public function test_can_generate_sitemap_helper_returns_false_if_hyde_does_not_have_base_url()
    {
        config(['hyde.site_url' => '']);
        $this->assertFalse(RssFeedService::canGenerateFeed());
    }

    public function test_can_generate_sitemap_helper_returns_false_if_sitemaps_are_disabled_in_config()
    {
        config(['hyde.site_url' => 'foo']);
        config(['hyde.generate_rss_feed' => false]);
        $this->assertFalse(RssFeedService::canGenerateFeed());
    }
}