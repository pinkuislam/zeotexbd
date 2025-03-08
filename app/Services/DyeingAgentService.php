<?php

namespace App\Services;

use App\Models\DyeingAgent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DyeingAgentService
{
    public static function due($dyeingAgentId)
    {
        // Dyeing Received + Received  - (Payment + Adjustment) 

        $dyeingAgent = DyeingAgent::select(DB::raw("((IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - IFNULL(C.amount, 0)) AS due"))
        ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_cost) AS amount FROM receive_dyeings  GROUP BY dyeing_agent_id) AS A"), function($q) {
            $q->on('dyeing_agents.id', '=', 'A.dyeing_agent_id');
        })
        ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS amount FROM dyeing_payments WHERE type = 'Received' GROUP BY dyeing_agent_id) AS B"), function($q) {
            $q->on('dyeing_agents.id', '=', 'B.dyeing_agent_id');
        })
        ->leftJoin(DB::raw("(SELECT dyeing_agent_id, SUM(total_amount) AS amount FROM dyeing_payments WHERE type != 'Received' GROUP BY dyeing_agent_id) AS C"), function($q) {
            $q->on('dyeing_agents.id', '=', 'C.dyeing_agent_id');
        })
        ->where('id', $dyeingAgentId)
        ->first();

        if ($dyeingAgent && $dyeingAgent->due) {
            return $dyeingAgent->due;
        }
        return 0;
    }

    
}