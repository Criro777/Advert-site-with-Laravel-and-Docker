<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Entity\Adverts\Attribute;
use App\Entity\Adverts\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Adverts\AttributeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Category $category)
    {
        $types = Attribute::typesList();

        return view('admin.adverts.categories.attributes.create', compact('category', 'types'));
    }

    /**
     * @param \App\Http\Requests\Admin\Adverts\AttributeRequest $request
     * @param \App\Entity\Adverts\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AttributeRequest $request, Category $category): RedirectResponse
    {
        $attribute = $category->attributes()->create([
            'name' => $request['name'],
            'type' => $request['type'],
            'required' => (bool)$request['required'],
            'variants' => array_map('trim', preg_split('#[\r\n]+#', $request['variants'])),
            'sort' => $request['sort'],
        ]);

        return redirect()->route('admin.adverts.categories.attributes.show', [$category, $attribute]);
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @param \App\Entity\Adverts\Attribute $attribute
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(Category $category, Attribute $attribute)
    {
        return view('admin.adverts.categories.attributes.show', compact('category', 'attribute'));
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @param \App\Entity\Adverts\Attribute $attribute
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Category $category, Attribute $attribute)
    {
        $types = Attribute::typesList();

        return view('admin.adverts.categories.attributes.edit', compact('category', 'attribute', 'types'));
    }

    /**
     * @param \App\Http\Requests\Admin\Adverts\AttributeRequest $request
     * @param \App\Entity\Adverts\Category $category
     * @param \App\Entity\Adverts\Attribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AttributeRequest $request, Category $category, Attribute $attribute): RedirectResponse
    {
        $category->attributes()->findOrFail($attribute->id)->update([
            'name' => $request['name'],
            'type' => $request['type'],
            'required' => (bool)$request['required'],
            'variants' => array_map('trim', preg_split('#[\r\n]+#', $request['variants'])),
            'sort' => $request['sort'],
        ]);

        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * @param \App\Entity\Adverts\Category $category
     * @param \App\Entity\Adverts\Attribute $attribute
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category, Attribute $attribute): RedirectResponse
    {
        $attribute->delete();

        return redirect()->route('admin.adverts.categories.show', $category);
    }
}
