<?php

namespace App\Livewire;

use App\Models\TodoList as ModelsTodoList;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $search;

    #[Rule('required|min:3|max:50')]
    public $editTodoName;
    public $editTodoID;



    public function create()
    {
        // validation only name
        $validation = $this->validateOnly('name');

        // create new todo
        ModelsTodoList::create($validation);

        // empty the input name
        $this->reset('name');

        // session flash when success the processiog
        session()->flash('success', 'Saved.');

        // when add the new todo note returned me to the first page
        $this->resetPage();
    }

    public function delete($todoID)
    {
        ModelsTodoList::find($todoID)->delete();
    }

    public function toggle($todoID)
    {
        $todo = ModelsTodoList::find($todoID);
        // if click on the check-box it's returned true
        // beacuse false the false is true
        $todo->completed = !$todo->completed;

        $todo->save();
    }

    public function edit($todoID)
    {
        $this->editTodoID = $todoID;
        $this->editTodoName = ModelsTodoList::find($todoID)->name;
    }

    public function update($todoID)
    {
        $this->validateOnly('editTodoName');
        ModelsTodoList::find($todoID)->update([
            'name' => $this->editTodoName
        ]);
        $this->cancel();
        session()->flash('success-msg', 'Todo Is Updated');
    }

    public function cancel()
    {
        $this->reset('editTodoName', 'editTodoID');
    }

    public function render()
    {
        $todos = ModelsTodoList::latest()->where('name', 'like', "%{$this->search}%")->paginate(5);
        return view('livewire.todo-list', [
            'todos' => $todos,
        ]);
    }
}
