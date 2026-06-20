using Microsoft.EntityFrameworkCore;
using WebApp.Data;
using WebApp.Models;
using WebApp.Models.ViewModels;

namespace WebApp.Services;

public class TaskService
{
    private readonly AppDbContext _db;

    public TaskService(AppDbContext db)
    {
        _db = db;
    }

    public async Task<List<TaskItem>> GetAllAsync(int userId)
    {
        return await _db.TaskItems
            .Where(t => t.UserId == userId)
            .OrderByDescending(t => t.CreatedAt)
            .ToListAsync();
    }

    public async Task<TaskItem?> GetByIdAsync(int id)
    {
        return await _db.TaskItems.FindAsync(id);
    }

    public async Task<TaskItem> CreateAsync(int userId, TaskViewModel model)
    {
        var task = new TaskItem
        {
            UserId = userId,
            Title = model.Title,
            Description = model.Description,
            Status = "pending",
            DueDate = model.DueDate,
            CreatedAt = DateTime.UtcNow,
            UpdatedAt = DateTime.UtcNow
        };

        _db.TaskItems.Add(task);
        await _db.SaveChangesAsync();
        return task;
    }

    public async Task<bool> UpdateAsync(int id, int userId, TaskViewModel model)
    {
        var task = await _db.TaskItems.FirstOrDefaultAsync(t => t.Id == id && t.UserId == userId);
        if (task == null) return false;

        task.Title = model.Title;
        task.Description = model.Description;
        task.Status = model.Status;
        task.DueDate = model.DueDate;
        task.UpdatedAt = DateTime.UtcNow;

        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<bool> DeleteAsync(int id, int userId)
    {
        var task = await _db.TaskItems.FirstOrDefaultAsync(t => t.Id == id && t.UserId == userId);
        if (task == null) return false;

        _db.TaskItems.Remove(task);
        await _db.SaveChangesAsync();
        return true;
    }

    public async Task<(int Total, int Pending, int Completed)> GetCountsAsync(int userId)
    {
        var total = await _db.TaskItems.CountAsync(t => t.UserId == userId);
        var pending = await _db.TaskItems.CountAsync(t => t.UserId == userId && t.Status == "pending");
        var completed = await _db.TaskItems.CountAsync(t => t.UserId == userId && t.Status == "completed");
        return (total, pending, completed);
    }
}
