<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Entity\Adverts\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Adverts\CategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::defaultOrder()->withDepth()->get();

        return view('admin.adverts.categories.index', compact('categories'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create()
    {
        $parents = Category::defaultOrder()->withDepth()->get();

        return view('admin.adverts.categories.create', compact('parents'));
    }

    /**
     * @param \App\Http\Requests\Admin\Adverts\CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'parent_id' => $request['parent'],
        ]);

        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(Category $category)
    {
        $parentAttributes = $category->parentAttributes();
        $attributes = $category->attributes()->orderBy('sort')->get();

        return view('admin.adverts.categories.show', compact('category', 'attributes', 'parentAttributes'));
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $parents = Category::defaultOrder()->withDepth()->get();

        return view('admin.adverts.categories.edit', compact('category', 'parents'));
    }

    /**
     * @param \App\Http\Requests\Admin\Adverts\CategoryRequest $request
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'parent_id' => $request['parent'],
        ]);

        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function first(Category $category): RedirectResponse
    {
        if ($first = $category->siblings()->defaultOrder()->first()) {
            $category->insertBeforeNode($first);
        }

        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function up(Category $category): RedirectResponse
    {
        $category->up();

        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function down(Category $category): RedirectResponse
    {
        $category->down();

        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function last(Category $category): RedirectResponse
    {
        if ($last = $category->siblings()->defaultOrder('desc')->first()) {
            $category->insertAfterNode($last);
        }

        return redirect()->route('admin.adverts.categories.index');
    }
}
