<?php namespace SevenShores\Kraken\Http\Controllers\Api;

use Illuminate\Http\Request;
use SevenShores\Kraken\Tag;
use SevenShores\Kraken\Contracts\Repositories\TagRepository;
use SevenShores\Kraken\Contracts\TransformerManager;
use SevenShores\Kraken\Http\Requests\StoreTagRequest;
use SevenShores\Kraken\Http\Requests\UpdateTagRequest;
use SevenShores\Kraken\Transformers\TagTransformer;

class TagsController extends ApiController
{
    /**
     * @var TagRepository
     */
    private $properties;

    /**
     * @param TransformerManager $manager
     * @param TagRepository $properties
     */
    public function __construct(TransformerManager $manager, TagRepository $properties)
    {
        parent::__construct($manager);
        $this->properties = $properties;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $options = [
            'current' => $request->get('cursor'),
            'count'   => $request->get('count'),
            'prev'    => $request->get('prev'),
        ];

        $properties = $this->properties->cursor($request->get('cursor'), $options);

        return $this->respondWithCursor($properties, new TagTransformer(), $options);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTagRequest $request
     * @return Response
     */
    public function store(StoreTagRequest $request)
    {
        $tag = new Tag();

        $data = [
            'name'        => $request->json('name'),
            'slug'        => $request->json('slug'),
            'description' => $request->json('description'),
        ];

        foreach ($data as $name => $value) {
            if (! is_null($value)) {
                $tag->$name = $value;
            }
        }

        $tag->save();

        return $this->respondWithItem($tag, new TagTransformer());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $tag = $this->properties->getById($id);

        return $this->respondWithItem($tag, new TagTransformer());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTagRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateTagRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $data = [
            'name'        => $request->json('name'),
            'slug'        => $request->json('slug'),
            'description' => $request->json('description'),
        ];

        foreach ($data as $name => $value) {
            if (! is_null($value)) {
                $tag->$name = $value;
            }
        }

        $tag->save();

        return $this->respondWithItem($tag, new TagTransformer());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $tag = $this->properties->getById($id);
        $tag->delete();

        return $this->respondWithItem($tag, new TagTransformer());
    }
}