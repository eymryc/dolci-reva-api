<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @var User
     */
    protected User $user;

    /**
     * User constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get all user.
     *
     * @return User $user
     */
    public function all()
    {
        return $this->user->get();
    }

    /**
     * Get all user with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->user->with('categories')->paginate($perPage);
    }

    /**
     * Get user by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->user->find($id);
    }

    /**
     * Save User
     *
     * @param $data
     * @return User
     */
    public function save(array $data)
    {
        // On sépare les services si présents
        $services = $data['services'] ?? [];

        // On retire les services du tableau de données utilisateur
        unset($data['services']);

        // Création du user
        $user = User::create($data);

        // Attacher les catégories si définies
        if (!empty($services)) {
            $user->categories()->attach($services);
        }

        // return $user;
        return $user;
    }

    /**
     * Update User
     *
     * @param $data
     * @return User
     */
    public function update(array $data, int $id)
    {
        // On sépare les services si présents
        $services = $data['services'] ?? [];

        // On retire les services du tableau de données utilisateur
        unset($data['services']);

        $user = $this->user->find($id);

        $user = $this->user->find($id);

        if ($user) {
            $user->update($data);

            // Attacher les catégories si définies
            if (!empty($services)) {
                $user->categories()->attach($services);
            }
        }

        return $user;
    }

    /**
     * Delete User
     *
     * @param $data
     * @return User
     */
    public function delete(int $id)
    {
        $user = $this->user->find($id);
        if ($user) {
            $user->delete();
        }
        return $user;
    }
}
