<?php  namespace SevenShores\Kraken\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use SevenShores\Kraken\Contracts\TransformerManager;

class FractalTransformerManager implements TransformerManager
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $manager;

    /**
     * @var Request
     */
    private $request;
    
    /**
     * @param Fractal $manager
     * @param SerializerAbstract $serializer
     * @param Request $request
     */
    public function __construct(Fractal $manager, SerializerAbstract $serializer, Request $request)
    {
        $this->manager = $manager;
        $this->request = $request;

        $this->manager->setSerializer($serializer);
        $this->manager->parseIncludes($request->query());
    }

    /**
     * @param string $includes
     */
    public function parseIncludes($includes)
    {
        $this->manager->parseIncludes($includes);
    }

    /**
     * @param $data
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param string $resourceKey
     * @return array
     */
    public function collection($data, $transformer = null, $resourceKey = null)
    {
        $resource = new Collection($data, $this->getTransformer($transformer), $resourceKey);

        return $this->manager->createData($resource)->toJson();
    }

    /**
     * @param $data
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param string $resourceKey
     * @return array
     */
    public function item($data, $transformer = null, $resourceKey = null)
    {
        $resource = new Item($data, $this->getTransformer($transformer), $resourceKey);

        return $this->manager->createData($resource)->toJson();
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param string $resourceKey
     * @return array
     */
    public function paginatedCollection(LengthAwarePaginator $paginator, $transformer = null, $resourceKey = null)
    {
        $paginator->appends($this->request->query());

        $resource = new Collection($paginator->items(), $this->getTransformer($transformer), $resourceKey);

        $resource->setPaginator($this->getPaginatorAdapter($paginator));

        return $this->manager->createData($resource)->toJson();
    }

    /**
     * @param TransformerAbstract $transformer
     * @return TransformerAbstract|callback
     */
    protected function getTransformer($transformer = null)
    {
        return $transformer ?: function ($data) {
            if ($data instanceof Arrayable) {
                return $data->toArray();
            }
            return (array) $data;
        };
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @return IlluminatePaginatorAdapter
     */
    protected function getPaginatorAdapter(LengthAwarePaginator $paginator)
    {
        $paginatorAdapter = new IlluminatePaginatorAdapter($paginator);

//        $queryParams = array_diff_key($_GET, array_flip(['page']));
//        foreach ($queryParams as $key => $value) {
//            $paginatorAdapter->addQuery($key, $value);
//        }

        return $paginatorAdapter;
    }
}