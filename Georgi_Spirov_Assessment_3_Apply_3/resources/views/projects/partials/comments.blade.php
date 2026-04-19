<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Comments</h4>
            <span class="text-muted">
                <span x-text="$store.comments.count"></span> total
            </span>
        </div>

        @auth
            @if(auth()->user()->hasVerifiedEmail())
                @can('comment', $project)
                    <form action="{{ route('project-comments.store') }}"
                          method="POST"
                          class="mb-4"
                          x-data="commentForm()"
                          @submit.prevent="handleSubmit"
                    >
                        @csrf

                        <input type="hidden" name="project_id" value="{{ $project->id }}">

                        <div class="mb-3">
                            <label for="project_comment[comment]" class="form-label">Leave a comment</label>
                            <textarea id="project_comment[comment]"
                                      name="comment"
                                      rows="4"
                                      x-model="comment"
                                      class="form-control"
                                      @input="delete backendErrors.comment"
                                      :class="{ 'is-invalid': hasBackendError('comment') || (shouldShow('comment') && (!hasFilledAttribute('comment') || !isCommentLongEnough())) }"
                                      placeholder="Write your thoughts about this project..."></textarea>

                            <div class="invalid-feedback" x-show="hasBackendError('comment')">
                                <template x-for="(error, index) in backendErrors.comment" :key="index">
                                    <div x-text="error"></div>
                                </template>
                            </div>

                            <div x-show="!hasBackendError('comment') && !isCommentLongEnough()" class="invalid-feedback">
                                {{ trans('validation.min.string', ['attribute' => 'comment', 'min' => 3]) }}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" :disabled="invalid() || submitted">
                            Post Comment
                        </button>
                    </form>
                @endcan
            @else
                <div class="alert alert-light border mb-4">
                    Verify your email address to leave a comment.
                </div>
            @endif
        @else
            <div class="alert alert-light border mb-4">
                <a href="{{ route('login') }}">Log in</a> to leave a comment.
            </div>
        @endauth

        <div x-data="paginatedComments({
            projectId: @js($project->id),
            comments: @js($comments),
            offset: @js(count($comments)),
            ableToDeleteCommentIds: @js($ableToDeleteCommentsIds)
        })"
             @comment-added.window="addComment($event.detail)"
        >
            <div x-show="comments.length === 0" class="alert alert-light border">
                No comments yet.
            </div>

            <div class="d-flex flex-column gap-3">
                <template x-for="comment in comments" :key="comment.id">
                    <div class="border rounded p-3 bg-light position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong x-text="comment.username"></strong><br>
                                <small class="text-muted" x-text="comment.created_at"></small>
                            </div>

                            <button x-show="ableToDeleteCommentIds.includes(comment.id)"
                                    class="btn btn-sm btn-light border rounded-circle"
                                    @click="confirm('Are you sure?'); deleteComment(comment.id)"
                            >
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </div>

                        <p class="mb-0" x-text="comment.comment"></p>
                    </div>
                </template>
            </div>

            <div x-show="loading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm" role="status"></div>
            </div>


            <div class="py-3 text-center" x-show="comments.length">
                <button x-show="!loading"
                        @click="loadMore"
                        class="btn btn-outline-primary"
                >
                    Load more comments
                </button>

                <div x-show="loading" class="text-muted mt-2">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>
