<?php

namespace App\Http\Controllers;

use App\Events\TodoCompleted;
use App\Events\TodoShared;
use App\Models\Category;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'is_completed' => ['nullable', 'boolean'],
            'ownership' => ['nullable', 'in:own,shared'],
        ]);

        $filters = [
            'category_id' => $request->query('category_id'),
            'is_completed' => $request->query('is_completed'),
            'ownership' => $request->query('ownership'),
        ];

        $categories = Category::all(['id', 'name']);

        $todos = Todo::query()
            ->with([
                'category:id,name',
                'shares:id,name',
                'user:id,name',
            ])
            ->when(!is_null($filters['category_id']), function (Builder $query) use ($filters) {
                $query->where('category_id', '=', (int) $filters['category_id']);
            })
            ->when(!is_null($filters['is_completed']), function (Builder $query) use ($filters) {
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

        return Inertia::render('Todos/Index', [
            'categories' => $categories,
            'filters' => $filters,
            'todos' => $todos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $categories = Category::all(['id', 'name']);

        $users = User::whereNot('id', $request->user()->id)->get(['id', 'name']);

        return Inertia::render('Todos/Create', [
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:32'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'shares' => ['array'],
            'shares.*' => ['nullable', 'distinct', 'exists:users,id', Rule::notIn([$request->user()->id])],
        ]);

        $todo = $request->user()->todos()->create($validated);

        $todo->shares()->sync($validated['shares']);

        if (count($validated['shares']) > 0) {
            TodoShared::dispatch($todo);
        }

        return redirect(route('todos.index'))->with('success', 'Todo created.');
    }

    public function markCompleted(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->id !== $todo->user_id && is_null($todo->shares()->find($request->user()->id))) {
            return redirect(route('todos.index'))
                ->with('error', 'You are not the owner of this todo, neither is it shared with you.');
        }

        $todo->update([
            'is_completed' => true,
        ]);

        TodoCompleted::dispatch($todo);

        return redirect()->back()->with('success', 'Todo completed.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Todo $todo): Response | RedirectResponse
    {
        if ($request->user()->id !== $todo->user_id) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to edit todo.');
        }

        $categories = Category::all(['id', 'name']);

        $todo->load(['shares:id']);

        $users = User::whereNot('id', $request->user()->id)->get(['id', 'name']);

        return Inertia::render('Todos/Edit', [
            'categories' => $categories,
            'todo' => $todo,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->id !== $todo->user_id) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to update todo.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:32'],
            'description' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'shares' => ['array'],
            'shares.*' => ['nullable', 'distinct', 'exists:users,id', Rule::notIn([$request->user()->id])],
        ]);

        $todo->update($validated);

        $todo->shares()->sync($validated['shares']);

        if (count($validated['shares']) > 0) {
            TodoShared::dispatch($todo);
        }

        return redirect(route('todos.index'))->with('success', 'Todo updated.');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo): RedirectResponse
    {
        if ($request->user()->id !== $todo->user_id) {
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
        if ($request->user()->id !== $todo->user_id) {
            return redirect(route('todos.index'))->with('error', 'Not authorized to restore todo.');
        }

        $todo->restore();

        return redirect()->back()->with('success', 'Todo restored.');
    }
}
