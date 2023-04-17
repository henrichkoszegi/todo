<?php

namespace App\Http\Controllers;

use App\Events\TodoCompleted;
use App\Events\TodoShared;
use App\Http\Requests\IndexTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexTodoRequest $request): Response
    {
        $categories = Category::all(['id', 'name']);

        $filters = [
            'category_id' => $request->query('category_id'),
            'is_completed' => $request->query('is_completed'),
            'ownership' => $request->query('ownership'),
        ];

        $todos = Todo::query()
            ->with([
                'category:id,name',
                'shares:id,name',
                'user:id,name',
            ])
            ->when(! is_null($filters['category_id']), function (Builder $query) use ($filters) {
                $query->where('category_id', '=', (int) $filters['category_id']);
            })
            ->when(! is_null($filters['is_completed']), function (Builder $query) use ($filters) {
                $query->where('is_completed', '=', (bool) $filters['is_completed']);
            });

        if ($filters['ownership'] === 'own') {
            $todos = $todos->where('user_id', '=', $request->user()->id);
        } else if ($filters['ownership'] === 'shared') {
            $todos = $todos->whereRelation('shares', 'user_id', '=', $request->user()->id);
        } else {
            $todos = $todos->where(function (Builder $query) use ($request) {
                $query->where('user_id', '=', $request->user()->id)
                    ->orWhereRelation('shares', 'user_id', '=', $request->user()->id);
            });
        }

        $todos = $todos->latest()->paginate(5)->withQueryString();

        return Inertia::render('Todos/Index', compact(['categories', 'filters', 'todos']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $categories = Category::all(['id', 'name']);

        $users = User::whereNot('id', $request->user()->id)->get(['id', 'name']);

        return Inertia::render('Todos/Create', compact(['categories', 'users']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $todo = $request->user()->todos()->create($validated);

        if (count($validated['shares']) > 0) {
            $todo->shares()->sync($validated['shares']);

            TodoShared::dispatch($todo);
        }

        return redirect(route('todos.index'))->with('success', 'Todo created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Todo $todo): Response | RedirectResponse
    {
        if ($request->user()->cannot('update', $todo)) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to edit todo.');
        }

        $categories = Category::all(['id', 'name']);

        $todo->load(['shares:id']);

        $users = User::whereNot('id', $request->user()->id)->get(['id', 'name']);

        return Inertia::render('Todos/Edit', compact(['categories', 'todo', 'users']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->cannot('update', $todo)) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to update todo.');
        }

        $validated = $request->validated();

        $todo->update($validated);

        $todo->shares()->sync($validated['shares']);

        if (count($validated['shares']) > 0) {
            TodoShared::dispatch($todo);
        }

        return redirect(route('todos.index'))->with('success', 'Todo updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->cannot('delete', $todo)) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to delete todo.');
        }

        $todo->delete();

        return redirect()->back()->with('success', 'Todo deleted.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->cannot('restore', $todo)) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to restore todo.');
        }

        $todo->restore();

        return redirect()->back()->with('success', 'Todo restored.');
    }

    /**
     * Mark the specified resource as completed.
     */
    public function markCompleted(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->cannot('markCompleted', $todo)) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to mark todo as completed.');
        }

        $todo->update([
            'is_completed' => true,
        ]);

        TodoCompleted::dispatch($todo);

        return redirect()->back()->with('success', 'Todo completed.');
    }
}
