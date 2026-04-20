<?php

namespace App\Domain\Client\Services;

use App\Domain\Client\Models\Client;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function create(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            // Cria o cliente
            $client = Client::create([
                'company_name'   => $data['company_name'],
                'trade_name'     => $data['trade_name'] ?? null,
                'cnpj'           => $data['cnpj'] ?? null,
                'email'          => $data['email'],
                'phone'          => $data['phone'] ?? null,
                'monthly_fee'    => $data['monthly_fee'],
                'contract_start' => $data['contract_start'] ?? null,
                'contract_end'   => $data['contract_end'] ?? null,
                'show_roi'       => isset($data['show_roi']) ? (bool) $data['show_roi'] : false,
                'status'         => $data['status'],
                'notes'          => $data['notes'] ?? null,
            ]);

            // Cria o usuário de acesso vinculado ao cliente
            $user = User::create([
                'client_id' => $client->id,
                'name'      => $data['user_name'],
                'email'     => $data['user_email'],
                'password'  => Hash::make($data['user_password']),
                'role'      => 'client',
                'is_active' => true,
            ]);
            $user->assignRole('client');

            // AuditLog::record('client.created', $client);

            return $client;
        });
    }

    public function update(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update([
                'company_name'   => $data['company_name'],
                'trade_name'     => $data['trade_name'] ?? null,
                'cnpj'           => $data['cnpj'] ?? null,
                'email'          => $data['email'],
                'phone'          => $data['phone'] ?? null,
                'monthly_fee'    => $data['monthly_fee'],
                'contract_start' => $data['contract_start'] ?? null,
                'contract_end'   => $data['contract_end'] ?? null,
                'show_roi'       => isset($data['show_roi']) ? (bool) $data['show_roi'] : false,
                'status'         => $data['status'],
                'notes'          => $data['notes'] ?? null,
            ]);

            // AuditLog::record('client.updated', $client);

            return $client->fresh();
        });
    }

    public function delete(Client $client): void
    {
        DB::transaction(function () use ($client) {
            // AuditLog::record('client.deleted', $client);
            $client->users()->delete();
            $client->delete();
        });
    }
}