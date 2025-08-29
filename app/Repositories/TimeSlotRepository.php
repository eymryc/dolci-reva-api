<?php
namespace App\Repositories;

use App\Models\TimeSlot;

class TimeSlotRepository
{
	 /**
     * @var TimeSlot
     */
    protected TimeSlot $timeSlot;

    /**
     * TimeSlot constructor.
     *
     * @param TimeSlot $timeSlot
     */
    public function __construct(TimeSlot $timeSlot)
    {
        $this->timeSlot = $timeSlot;
    }

    /**
     * Get all timeSlot.
     *
     * @return TimeSlot $timeSlot
     */
    public function all()
    {
        return $this->timeSlot->get();
    }

    /**
     * Get all timeSlot with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->timeSlot->paginate($perPage);
    }

     /**
     * Get timeSlot by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->timeSlot->find($id);
    }

    /**
     * Save TimeSlot
     *
     * @param $data
     * @return TimeSlot
     */
     public function save(array $data)
    {
        return TimeSlot::create($data);
    }

     /**
     * Update TimeSlot
     *
     * @param $data
     * @return TimeSlot
     */
    public function update(array $data, int $id)
    {
        $timeSlot = $this->timeSlot->find($id);
        $timeSlot->update($data);
        return $timeSlot;
    }

    /**
     * Delete TimeSlot
     *
     * @param $data
     * @return TimeSlot
     */
   	 public function delete(int $id)
    {
        $timeSlot = $this->timeSlot->find($id);
        $timeSlot->delete();
        return $timeSlot;
    }
}
