<?php

namespace App\Http\Controllers;

use App\Enums\ProjectPhaseEnum;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the list of projects on the home page.
     *
     * This method retrieves and prepares project data for the home page view,
     * applying user-specified filters, sorting, and pagination.
     */
    public function index(Request $request): View
    {
        $sort = $request->input('sort');

        // Query projects and apply eager loading for related data.
        // The query also calculates average ratings and counts the number of users
        // who have marked each project as a favorite.
        $projects = Project::query()
                           ->with(['user', 'images'])
                           ->withAvg('ratings', 'rating')
                           ->withCount('favouriteByUsers')
                           // Apply a filter for the 'title' field if the parameter is present.
                           ->when($request->filled('title'), function (Builder $query) use ($request) {
                               $query->where(
                                   'title',
                                   'like',
                                   sprintf('%%%s%%', $request->input('title'))
                               );
                           })
                           // Apply a filter with wildcards for the 'short_description' field if the parameter is present.
                           ->when($request->filled('short_description'), function (Builder $query) use ($request) {
                               $query->where(
                                   'short_description',
                                   'like',
                                   sprintf('%%%s%%', $request->input('short_description'))
                               );
                           })
                           // Filter projects by their phase if provided.
                           ->when($request->filled('phase'), function (Builder $query) use ($request) {
                               $query->where('phase', '=', $request->input('phase'));
                           })
                           // Filter by the start date if applicable.
                           ->when($request->filled('start_date'), function (Builder $query) use ($request) {
                               $query->whereDate('start_date', '>=', $request->input('start_date'));
                           })
                           // Filter by the end date if applicable.
                           ->when($request->filled('end_date'), function (Builder $query) use ($request) {
                               $query->whereDate('end_date', '<=', $request->input('end_date'));
                           })
                           // Sort projects by creation date (oldest first) if specified.
                           ->when($sort === 'oldest', function (Builder $query) {
                               $query->orderBy('created_at');
                           })
                           // Sort projects by their average rating in descending order.
                           ->when($sort === 'highest_rated', function (Builder $query) {
                               $query->orderByDesc('ratings_avg_rating');
                           })
                           // Sort projects by the number of favorites in descending order.
                           ->when($sort === 'most_liked', function (Builder $query) {
                               $query->orderByDesc('favourite_by_users_count');
                           })
                           // Sort projects alphabetically by their title in ascending order.
                           ->when($sort === 'title_asc', function (Builder $query) {
                               $query->orderBy('title');
                           })
                           // Sort projects alphabetically by their title in descending order.
                           ->when($sort === 'title_desc', function (Builder $query) {
                               $query->orderBy('title', 'desc');
                           })
                           // Default sorting: by latest ID if no valid sorting option is provided.
                           ->when(!in_array($sort, ['oldest', 'highest_rated', 'title_asc', 'title_desc']), function ($query) {
                               $query->latest('id');
                           });

        return view('home.index', [
            'phases' => ProjectPhaseEnum::cases(),
            'projects' => $projects->paginate(9),
        ]);
    }
}
