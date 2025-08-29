<?php

namespace App\Repositories;

use App\Models\Space;

class SpaceRepository
{
    /**
     * @var Space
     */
    protected Space $space;

    /**
     * Space constructor.
     *
     * @param Space $space
     */
    public function __construct(Space $space)
    {
        $this->space = $space;
    }

    /**
     * Get all space.
     *
     * @return Space $space
     */
    public function all()
    {
        return $this->space->get();
    }
    /**
     * Get all space with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->space->paginate($perPage);
    }

    /**
     * Get space by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->space->find($id);
    }

    /**
     * Save Space
     *
     * @param $data
     * @return Space
     */
    public function save(array $data)
    {
        // Convert the type to a string if it's an enum
        $data['type'] = $data['type']->value;
        return Space::create($data);
    }

    /**
     * Update Space
     *
     * @param $data
     * @return Space
     */
    public function update(array $data, int $id)
    {

        $space = $this->space->find($id);
        // Convert the type to a string if it's an enum
        $data['type'] = $data['type']->value;
        $space->update($data);
        return $space;
    }

    /**
     * Delete Space
     *
     * @param $data
     * @return Space
     */
    public function delete(int $id)
    {
        $space = $this->space->find($id);
        $space->delete();
        return $space;
    }
}
