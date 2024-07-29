<?php

namespace App\Http\Controllers;

use App\Services\MondayService;
use Inertia\Inertia;

class MondayController extends Controller
{
    protected $mondayService;

    public function __construct(MondayService $mondayService)
    {
        $this->mondayService = $mondayService;
    }

    public function getBoards()
    {
        $query = '{
              me {
                is_guest
                created_at
                name
                id
              }
              boards (ids: [5639141697]) {
                items_page {
                  items {

                    name

                    subscribers {
                      name
                    }
                        column_values {
                  column {
                    id
                    title
                  }
                  id
                  type
                  value
                }
                  }
                }
              }
            }';

        $response = $this->mondayService->query($query);

        // For Inertia view
        return Inertia::render('Welcome', ['data' => $response]);
    }


}
