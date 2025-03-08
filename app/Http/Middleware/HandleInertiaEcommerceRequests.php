<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Page;

class HandleInertiaEcommerceRequests extends HandleInertiaRequests
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'ecommerce.inertia';

    protected function getNavbarCategories()
    {
        $categories = Category::with('childs', 'childs.childs')->where('parent_id', null)->where('status', 'Active')->get(['id', 'parent_id', 'name', 'slug']);
        return $categories;
    }

    protected function getFooterPages()
    {
        $pages = Page::where('status', 'Active')->get(['id', 'slug', 'title']);
        return $pages;
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $siteSettings = SiteSetting::first();
        view()->share(compact('siteSettings'));
        return array_merge(parent::share($request), [
            'auth.user' => function () use ($request) {
                if ($request->user()) {
                    $user = $request->user();
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                }
                return null;
            },
            'flash' => [
                'message' => function () use ($request) {
                    return $request->session()->pull('message');
                },
                'successMessage' => function () use ($request) {
                    return $request->session()->pull('successMessage');
                },
                'errorMessage' => function () use ($request) {
                    return $request->session()->pull('errorMessage');
                },
            ],
            'config' => [
                'site_settings' => $siteSettings,
                'app' => [
                    'name' => config('app.name'),
                ],
            ],
            'navbar_categories' => $this->getNavbarCategories(),
            'footer_pages' => $this->getFooterPages(),
            'cart' => session('cart'),
        ]);
    }
}
