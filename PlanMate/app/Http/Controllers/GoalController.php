<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Http\Resources\GoalResource;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    public function data(): JsonResponse
    {
        $goals = $this->query()->orderBy('id')->get();
        return response()->json(GoalResource::collection($goals)->resolve());
    }

    public function store(GoalRequest $request): JsonResponse
    {
        $goal = DB::transaction(function () use ($request) {
            $goal = Goal::create($request->atribut() + ['user_id' => Auth::id(), 'progres' => 0]);
            $goal->gantiTahapan($request->input('targetKecil', []));
            $goal->sinkronProgres((int) $request->input('progres', 0));
            return $goal;
        });

        return $this->respond($goal, 201);
    }

    public function update(GoalRequest $request, int $id): JsonResponse
    {
        $goal = $this->ownGoal($id);

        DB::transaction(function () use ($goal, $request) {
            $goal->update($request->atribut());
            $goal->gantiTahapan($request->input('targetKecil', []));
            $goal->sinkronProgres((int) $request->input('progres', 0));
        });

        return $this->respond($goal);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->ownGoal($id)->delete();
        return response()->json(null, 204);
    }

    public function progress(Request $request, int $id): JsonResponse
    {
        $goal = $this->ownGoal($id);
        $p = $request->validate(['progres' => ['required', 'integer', 'between:0,100']])['progres'];

        $goal->setProgres((int) $p);
        return $this->respond($goal);
    }

    public function toggleTahapan(int $id, int $tahapanId): JsonResponse
    {
        $goal = $this->ownGoal($id);
        $tahapan = $goal->tahapan()->findOrFail($tahapanId);

        $tahapan->update(['done' => ! $tahapan->done]);
        $goal->sinkronProgres();

        return $this->respond($goal);
    }

    // ---- helper kecil ----
    private function query()
    {
        return Goal::milikUser(Auth::id())->with(['tahapan', 'aktivitas']);
    }

    private function ownGoal(int $id): Goal
    {
        return $this->query()->findOrFail($id);
    }

    private function respond(Goal $goal, int $status = 200): JsonResponse
    {
        return response()->json(
            GoalResource::make($goal->fresh(['tahapan', 'aktivitas']))->resolve(),
            $status
        );
    }
}
