<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeddingReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_code',
        'bride',
        'groom',
        'wedding_organizer',
        'wedding_date',
        'communication_efficiency',
        'workflow_planning',
        'material_preparation',
        'service_attitude',
        'execution_of_workflow',
        'time_management',
        'guest_care',
        'team_coordination',
        'third_party_coordination',
        'problem_solving_ability',
        'wrap_up_and_item_check',
        'status',
        'customer_name',
        'couple_mood',
        'customer_review',
    ];

    public static function weddingStats()
    {
        return self::where('status', 'accepted')
        ->selectRaw('
            AVG(communication_efficiency) as communication_efficiency,
            AVG(workflow_planning) as workflow_planning,
            AVG(material_preparation) as material_preparation,
            AVG(service_attitude) as service_attitude,
            AVG(execution_of_workflow) as execution_of_workflow,
            AVG(time_management) as time_management,
            AVG(guest_care) as guest_care,
            AVG(team_coordination) as team_coordination,
            AVG(third_party_coordination) as third_party_coordination,
            AVG(problem_solving_ability) as problem_solving_ability,
            AVG(wrap_up_and_item_check) as wrap_up_and_item_check
        ')->first();
    }
}
