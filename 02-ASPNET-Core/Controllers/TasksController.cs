using Microsoft.AspNetCore.Mvc;
using WebApp.Models.ViewModels;
using WebApp.Services;

namespace WebApp.Controllers;

public class TasksController : Controller
{
    private readonly TaskService _taskService;
    private readonly AuthService _auth;

    public TasksController(TaskService taskService, AuthService auth)
    {
        _taskService = taskService;
        _auth = auth;
    }

    private IActionResult? RedirectIfNotLoggedIn()
    {
        if (!_auth.IsLoggedIn()) return RedirectToAction("Login", "Auth");
        return null;
    }

    public async Task<IActionResult> Index()
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login", "Auth");

        var tasks = await _taskService.GetAllAsync(userId.Value);
        return View(tasks);
    }

    public IActionResult Create()
    {
        if (!_auth.IsLoggedIn()) return RedirectToAction("Login", "Auth");
        return View();
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Create(TaskViewModel model)
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login", "Auth");

        if (!ModelState.IsValid) return View(model);

        await _taskService.CreateAsync(userId.Value, model);
        return RedirectToAction("Index");
    }

    public async Task<IActionResult> Edit(int id)
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login", "Auth");

        var task = await _taskService.GetByIdAsync(id);
        if (task == null || task.UserId != userId) return NotFound();

        var model = new TaskViewModel
        {
            Id = task.Id,
            Title = task.Title,
            Description = task.Description,
            Status = task.Status,
            DueDate = task.DueDate
        };

        return View(model);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Edit(int id, TaskViewModel model)
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login", "Auth");

        if (!ModelState.IsValid) return View(model);

        var updated = await _taskService.UpdateAsync(id, userId.Value, model);
        if (!updated) return NotFound();

        return RedirectToAction("Index");
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Delete(int id)
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login", "Auth");

        await _taskService.DeleteAsync(id, userId.Value);
        return RedirectToAction("Index");
    }
}
