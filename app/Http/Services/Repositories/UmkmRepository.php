<?php

namespace App\Http\Services\Repositories;

use App\Http\Services\Repositories\BaseRepository;
use App\Http\Services\Repositories\Contracts\UmkmContract;
use App\Models\Todo;
use App\Models\Umkm;

class UmkmRepository extends BaseRepository implements UmkmContract
{
	/**
	 * @var
	 */
	protected $model;

	public function __construct(Umkm $model)
	{
		$this->model = $model;
	}

	public function paginated(array $criteria)
	{
		$perPage = $criteria['per_page'] ?? 5;
		$field = $criteria['sort_field'] ?? 'id';
		$sortOrder = $criteria['sort_order'] ?? 'desc';
		return $this->model->orderBy($field, $sortOrder)->paginate($perPage);
	}

}