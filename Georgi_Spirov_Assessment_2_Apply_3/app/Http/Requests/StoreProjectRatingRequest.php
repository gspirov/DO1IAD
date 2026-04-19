<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\ProjectRating;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRatingRequest extends FormRequest
{
    /**
     * Define the validation rules for storing a project rating.
     */
    public function rules(): array
	{
		return [
            // - `project_id` is required and must exist in the `projects` table.
            'project_id' => ['required', 'exists:projects,id'],
            // - `rating` is required only if no previous rating exists
            //    can be null in case when deleting existing one
            //    and must be an integer between 1 and 5.
            'rating' => [
                Rule::requiredIf(fn () => $this->ratingIsRequired()),
                'nullable',
                'integer',
                'between:1,5',
            ],
		];
	}

    protected function prepareForValidation(): void
    {
        $projectId = $this->input('project_id');

        if (!$projectId) {
            return;
        }

        $this->merge([
            'project' => Project::find($projectId),
        ]);
    }

    protected function ratingIsRequired(): bool
    {
        $projectId = $this->input('project_id');

        if (!$projectId) {
            return true;
        }

        return ProjectRating::query()
                            ->where('user_id', '=', $this->user()->id)
                            ->where('project_id', '=', $projectId)
                            ->doesntExist();
    }

	public function authorize(): bool
	{
        if (!$this->project) {
            return false;
        }

        return $this->user()->can('rate', $this->project);
	}
}
