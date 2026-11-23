<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Video;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $trending = Product::active()->where('is_trending', true)->with('category')->get();
        seo()->page('home')->productList($trending, 'Trending Now');

        return view('pages.home', [
            'videos' => Video::where('is_active', true)->orderBy('sort')->get(),
            'trendingProducts' => $trending,
            'homeCategories' => Category::active()->where('show_on_home', true)->with('activeProducts')->get(),
            'collectionCategories' => Category::active()->where('show_in_collections', true)->get(),
        ]);
    }

    public function about()
    {
        seo()->page('about')->breadcrumbs([['About Us', '/about']])->schema(['@type' => 'AboutPage', 'url' => \App\Support\Seo::baseUrl().'/about', 'about' => ['@id' => \App\Support\Seo::baseUrl().'/#business']]);

        return view('pages.about', [
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort')->get(),
        ]);
    }

    public function contact()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort')->get();
        seo()->page('contact')->breadcrumbs([['Contact', '/contact']])->faqList($faqs)
            ->schema(['@type' => 'ContactPage', 'url' => \App\Support\Seo::baseUrl().'/contact', 'about' => ['@id' => \App\Support\Seo::baseUrl().'/#business']]);

        return view('pages.contact', ['faqs' => $faqs]);
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'inquiry_type' => 'nullable|string|max:120',
            'event_date' => 'nullable|date',
            'guests' => 'nullable|string|max:40',
            'message' => 'required|string|max:3000',
            'whatsapp_copy' => 'nullable|boolean',
        ]);
        $data['whatsapp_copy'] = $request->boolean('whatsapp_copy');
        ContactMessage::create($data + attribution());
        track_event('generate_lead', ['form' => 'contact', 'inquiry_type' => $data['inquiry_type'] ?? null]);

        return redirect()->to(route('contact').'#contact-form')->with('contact_sent', true);
    }

    public function menu(?string $slug = null)
    {
        $categories = Category::active()->get();
        $current = $slug ? $categories->firstWhere('slug', $slug) : null;
        abort_if($slug && ! $current, 404);

        $groups = [];
        foreach ($current ? [$current] : $categories as $cat) {
            $groups[] = [
                'slug' => $cat->slug,
                'title' => $cat->name,
                'eyebrow' => $cat->eyebrow,
                'icon' => $cat->eyebrow_icon ?: 'bakery_dining',
                'subtitle' => $cat->subtitle,
                'price_label' => $cat->price_label ?: 'Price',
                'products' => $cat->activeProducts()->get(),
            ];
        }
        if (! $current) {
            $loose = Product::active()->whereNull('category_id')->get();
            if ($loose->count()) {
                $groups[] = ['slug' => 'more', 'title' => 'More Treats', 'eyebrow' => null, 'icon' => 'cookie', 'subtitle' => null, 'price_label' => 'Price', 'products' => $loose];
            }
        }

        $products = collect($groups)->flatMap(fn ($g) => $g['products']);
        if ($current) {
            seo()->page('menu')->set([
                'title' => $current->meta_title ?: $current->name.' — Order Online',
                'description' => $current->meta_description ?: ($current->subtitle ?: $current->card_description),
                'image' => $current->image,
            ])->breadcrumbs([['Menu', '/menu'], [$current->name, '/menu/'.$current->slug]]);
        } else {
            seo()->page('menu')->breadcrumbs([['Menu', '/menu']]);
        }
        seo()->productList($products->take(30), $current->name ?? 'Menu');

        return view('pages.menu', compact('categories', 'current', 'groups', 'products'));
    }

    public function policy(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->with('sections')->firstOrFail();
        seo()->set(['title' => $page->meta_title ?: $page->title, 'description' => $page->meta_description ?: $page->subtitle])
            ->breadcrumbs([[$page->title, '/page/'.$page->slug]]);

        return view('pages.policy', compact('page'));
    }
}
