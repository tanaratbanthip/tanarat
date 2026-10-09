<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    // สร้าง XML Sitemap สำหรับ Search Engine Crawlers
    public function sitemap(): Response
    {
        $posts = Post::latest()->get();
        $baseUrl = config('app.url', url('/'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // หน้าแรก
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($baseUrl) . '</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // หน้ารวมบทความ
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($baseUrl . '/posts') . '</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';

        // ลูปดึง URL ของบทความทั้งหมด
        foreach ($posts as $post) {
            $postUrl = $baseUrl . '/posts/' . ($post->slug ?: $post->id);
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($postUrl) . '</loc>';
            $xml .= '<lastmod>' . $post->updated_at->toAtomString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    // สร้าง RSS 2.0 Feed สำหรับโปรแกรมอ่านข่าวและ RSS Readers
    public function feed(): Response
    {
        $posts = Post::with(['user', 'category'])->latest()->take(20)->get();
        $baseUrl = config('app.url', url('/'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>MyNotes Blog</title>';
        $xml .= '<link>' . htmlspecialchars($baseUrl) . '</link>';
        $xml .= '<description>พื้นที่จดบันทึก แลกเปลี่ยนความรู้ด้านการพัฒนาเว็บและเทคโนโลยี</description>';
        $xml .= '<language>th-TH</language>';
        $xml .= '<atom:link href="' . htmlspecialchars($baseUrl . '/feed') . '" rel="self" type="application/rss+xml" />';

        foreach ($posts as $post) {
            $postUrl = $baseUrl . '/posts/' . ($post->slug ?: $post->id);
            $description = htmlspecialchars(strip_tags($post->content));

            $xml .= '<item>';
            $xml .= '<title>' . htmlspecialchars($post->title) . '</title>';
            $xml .= '<link>' . htmlspecialchars($postUrl) . '</link>';
            $xml .= '<guid isPermaLink="true">' . htmlspecialchars($postUrl) . '</guid>';
            $xml .= '<pubDate>' . $post->created_at->toRssString() . '</pubDate>';
            $xml .= '<category>' . htmlspecialchars($post->category ? $post->category->name : 'ทั่วไป') . '</category>';
            $xml .= '<description>' . $description . '</description>';
            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}