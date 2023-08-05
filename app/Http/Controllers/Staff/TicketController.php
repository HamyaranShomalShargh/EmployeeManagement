<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TicketController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Tickets");
        try {
            return view("staff.tickets",["tickets" => TicketRoom::AllTickets()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Tickets");
        try {
            DB::beginTransaction();
            $request->validate(["message" => "required"],["message.required" => "درج متن پیام الزامی می باشد"]);
            Ticket::query()->findOrFail($id)->update(["message" => $request->input("message")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);

        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Tickets");
        try {
            DB::beginTransaction();
            $ticket = Ticket::query()->findOrFail($id);
            if ($ticket->attachment)
                Storage::disk("ticket_attachments")->delete($ticket->room_id,$ticket->attachment);
            $ticket->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroyAll($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Tickets");
        try {
            DB::beginTransaction();
            $room = TicketRoom::query()->with("tickets")->findOrFail($id);
            foreach ($room->tickets as $ticket)
                if ($ticket->attachment)
                    Storage::disk("ticket_attachments")->delete($ticket->room_id,$ticket->attachment);
            $room->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
