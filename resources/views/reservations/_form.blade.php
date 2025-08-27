<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Experiment</label>
        <select name="experiment_id" class="form-select" required>
            <option value="">— Select experiment —</option>
            @foreach($experiments as $ex)
            <option value="{{ $ex->id }}">{{ $ex->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select" required>
            <option value="">— Select user —</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Start time</label>
        <input type="datetime-local" name="start_time" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">End time</label>
        <input type="datetime-local" name="end_time" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="pending">pending</option>
            <option value="active">active</option>
            <option value="postponed">postponed</option>
            <option value="completed">completed</option>
            <option value="cancelled">cancelled</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Notes (optional)"></textarea>
    </div>
</div>