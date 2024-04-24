<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\GHLController;
use App\Http\Requests\CreateSupportTicketRequest;
use App\Http\Resources\SupportCategoryResource;
use App\Http\Resources\SupportTicketResource;
use App\Models\Location;
use App\Models\SupportCategory;
use App\Models\SupportTicket;
use Exception;

class SupportCategoryController extends BaseController
{
    protected $ghl_controller;

    public function __construct(GHLController $ghl_controller){
        $this->ghl_controller = $ghl_controller;
    }

    public function index(){
        try {
            $support_categories = SupportCategory::with('docMetas')->orderBy('name')->paginate(25);

            $data = [
                'supportCategories' => SupportCategoryResource::collection($support_categories),
                'pagination' => [
                    'current_page' => $support_categories->currentPage(),
                    'per_page' => $support_categories->perPage(),
                    'total' => $support_categories->total(),
                    'prev_page_url' => $support_categories->previousPageUrl(),
                    'next_page_url' => $support_categories->nextPageUrl(),
                    'first_page_url' => $support_categories->url(1),
                    'last_page_url' => $support_categories->url($support_categories->lastPage()),
                ],
            ];

            return $this->sendResponse($data, 'Support categories fetched successfully');
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

    public function createSupportTicket(CreateSupportTicketRequest $request){ 
        try {
            $data = $request->validated();
            $ticket = $data['ticket'];
            $contact = $data['contact'];

            $location = Location::where('go_high_level_location_id', $ticket['accountId'])
                ->first();

            if ($location) {
                $support_ticket = SupportTicket::create([
                    'subject' => $ticket['subject'],
                    'issue' => $ticket['issue'],
                    'video' => $ticket['video'] ?? null,
                    'account_id' => $ticket['accountId'],
                    'description' => $ticket['description'] ?? null,
                    'status' => TicketStatus::OPEN, 
                    'location_id' => $location->id,
                ]);

                $this->ghl_controller->createTask($contact, array_merge(['id' => $support_ticket->id], $ticket));
                return $this->sendResponse(new SupportTicketResource($support_ticket), 'Support ticket created successfully');
            } else {
                return $this->sendError('Location Not found.', [], 400);
            }
        } catch (Exception $error) {
            return $this->sendError($error->getMessage(), [], 500);
        }
    }

}