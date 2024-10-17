<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\ListAllRequest;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TagController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(ListAllRequest $request)
    {
        $user = $request->user();

        $tags = Tag::query()
            ->forUser($user->id)
            ->paginate($request->limit, ['*'], 'page', $request->page);

        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $user = $request->user();

        $tag = $user->tags()->create(['name' => $request->name]);

        return TagResource::make($tag)->response()->setStatusCode(201);
    }

    /*
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return TagResource::make($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Tag $tag)
    {
        // Authorization is handled in UpdateRequest via TagPolicy

        $tag->update(['name' => $request->name]);

        return TagResource::make($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return response()->json(null, 204); // 204 No Content
    }
}
