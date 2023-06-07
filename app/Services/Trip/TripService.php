<?php

namespace App\Services\Trip;

use App\Events\TripAccepted;
use App\Models\Trip;
use App\Repositories\Trip\TripRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TripService implements TripServiceInterface
{

    public function __construct(private TripRepositoryInterface $repository)
    {
    }

    public function store(ParameterBag $data): Trip
    {
        $user = Auth::user();

        $data->set('user_id' , $user->id);

        return $this->repository->store($data);
    }

    public function get(int $id): ?Trip
    {
        $trip = $this->repository->get($id);

        if (!$trip) {
            throw new HttpException(404, 'Cannot find trip');
        }

        TripAccepted::dispatch($trip, Auth::user());

        return $trip;
    }

    public function accept(int $id, ParameterBag $data): Trip
    {
        $data->set('driver_id', Auth::user()->id);

        $options = new ParameterBag([
            'relations' => ['driver.user']
        ]);

        return $this->repository->accept($id, $data, $options);
    }

    public function start(int $id, ParameterBag $data): Trip
    {
        $data->add([
            'is_started' => true,
        ]);

        $options = new ParameterBag([
            'relations' => ['driver.user']
        ]);

        return $this->repository->start($id, $data, $options);
    }


    public function end(int $id, ParameterBag $data): Trip
    {
        $data->add([
            'is_complete' => true,
        ]);

        $options = new ParameterBag([
            'relations' => ['driver.user']
        ]);

        return $this->repository->start($id, $data, $options);
    }

    public function location(int $id, ParameterBag $data): Trip
    {
        $options = new ParameterBag([
            'relations' => ['driver.user']
        ]);

        return $this->repository->start($id, $data, $options);
    }

}
