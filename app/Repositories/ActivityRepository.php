<?php

namespace App\Repositories;

use App\Models\Activity;

class ActivityRepository
{
    /**
     * @var Activity
     */
    protected Activity $activity;

    /**
     * Activity constructor.
     *
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * Get all activity.
     *
     * @return Activity $activity
     */
    public function all()
    {
        return $this->activity->get();
    }

    /**
     * Get all activity with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->activity->paginate($perPage);
    }

    /**
     * Get activity by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->activity->find($id);
    }

    /**
     * Save Activity
     *
     * @param $data
     * @return Activity
     */
    public function save(array $data)
    {
        return Activity::create($data);
    }

    /**
     * Update Activity
     *
     * @param $data
     * @return Activity
     */
    public function update(array $data, int $id)
    {
        $activity = $this->activity->find($id);
        $activity->update($data);
        return $activity;
    }

    /**
     * Delete Activity
     *
     * @param $data
     * @return Activity
     */
    public function delete(int $id)
    {
        $activity = $this->activity->find($id);
        $activity->delete();
        return $activity;
    }
}
