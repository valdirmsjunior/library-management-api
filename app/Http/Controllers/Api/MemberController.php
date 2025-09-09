<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberCollection;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');

        // Apply search filters, colocar esse trecho no service
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $members = $query->paginate(10);

        return $this->successResponse(new MemberCollection($members), 'Membros listados com sucesso', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        try {
            $member = Member::create($request->validated());
            return $this->successResponse(new MemberResource($member), 'Membro adicionado com sucesso', 201);
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar Membro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao adicionar Membro', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $member = Member::with(['activeBorrowings', 'borrowings'])->find($id);
            if (!$member) {
                return $this->errorResponse('Membro não encontrado', 404);
            }
            return $this->successResponse(new MemberResource($member), 'Membro recuperado com sucesso', 200);
        } catch (\Exception $e) {
            Log::error('Erro ao recuperar Membro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao recuperar Membro', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, string $id): JsonResponse
    {
        try {
            $member = Member::find($id);
            if (!$member) {
                return $this->errorResponse('Membro não encontrado', 404);
            }

            $member->update($request->validated());
            return $this->successResponse(new MemberResource($member), 'Membro atualizado com sucesso', 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar Membro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao atualizar Membro', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $member = Member::find($id);
            if (!$member) {
                return $this->errorResponse('Membro não encontrado', 404);
            }

            // Verifica se o membro tem empréstimos ativos
            if ($member->activeBorrowings()->count() > 0) {
                return $this->errorResponse('Não é possível deletar o membro com empréstimos ativos', 422);
            }

            $member->delete();
            return $this->successResponse(null, 'Membro deletado com sucesso', 200);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar Membro: ' . $e->getMessage());
            return $this->errorResponse('Erro ao deletar Membro', 500);
        }
    }
}
