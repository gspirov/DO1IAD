<?php

namespace App\Http\Requests;

use App\Enums\ProjectPhaseEnum;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProjectRequest extends FormRequest
{
	public function rules(): array
	{
		return [
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'short_description' => ['required', 'string'],
            'phase' => ['required', new Enum(ProjectPhaseEnum::class)]
		];
	}

    /**
     * Authorize whether the user is allowed to create/update project.
     * Everyone can create a project, only the owner can update it.
     */
	public function authorize(): bool
	{
        $project = $this->route('project');

        return $this->user()->can(
            null === $project ? 'create' : 'update',
            null === $project ? Project::class : $project
        );
	}
}
