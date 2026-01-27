<?php
  
namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\Team;
  
class Wizard extends Component
{
    public $currentStep = 1;
    public $name, $price, $detail, $status = 1;
    public $successMsg = '';
  
    /**
     * Write code on Method
     */
    public function render()
    {
        return view('livewire.wizard');
    }
  
    /**
     * Write code on Method
     */
    public function firstStepSubmit()
    {
        $validatedData = $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'detail' => 'required',
        ]);
 
        $this->currentStep = 2;
    }
  
    /**
     * Write code on Method
     */
    public function secondStepSubmit()
    {
        $validatedData = $this->validate([
            'status' => 'required',
        ]);
  
        $this->currentStep = 3;
    }
  
    /**
     * Write code on Method
     */
    public function submitForm()
    {
        Team::create([
            'name' => $this->name,
            'price' => $this->price,
            'detail' => $this->detail,
            'status' => $this->status,
        ]);
  
        $this->successMsg = 'Team successfully created.';
  
        $this->clearForm();
  
        $this->currentStep = 1;
    }
  
    /**
     * Write code on Method
     */
    public function back($step)
    {
        $this->currentStep = $step;    
    }
  
    /**
     * Write code on Method
     */
    public function clearForm()
    {
        $this->name = '';
        $this->price = '';
        $this->detail = '';
        $this->status = 1;
    }
}