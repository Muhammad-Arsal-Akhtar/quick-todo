<?php

namespace App\Livewire;
use Livewire\Attributes\Validate;
use App\Models\Todo;
use Livewire\WithPagination;

use Livewire\Component;

class TodoList extends Component
{
    use WithPagination;

    #[Validate('required|min:3')] 
    public $name;

    public $search;

    public $editTodoId;

    #[Validate('required|min:3')] 
    public $editTodoName;


    public function createItem(){

        $validateData = $this->validateOnly('name');

        Todo::create($validateData);

        $this->reset('name'); 

        session()->flash('success', 'Item Added Successfully');
    }

    public function deleteItem($todoId){
        Todo::where('id', $todoId)->delete();
    }

    public function toggledItem($todoId){
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function editIdName($todId){
        $this->editTodoId = $todId;
        $this->editTodoName = Todo::find($todId)->name;
    }

    public function cancelEdit(){
        $this->reset('editTodoId', 'editTodoName'); 
    }

    public function updateItem(){

        $this->validateOnly('editTodoName');

        Todo::find($this->editTodoId)->update([
            'name' => $this->editTodoName
        ]);

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name','LIKE',"%{$this->search}%")->paginate(3)
        ]);
    }
}
