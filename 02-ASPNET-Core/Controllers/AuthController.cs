using Microsoft.AspNetCore.Mvc;
using WebApp.Models.ViewModels;
using WebApp.Services;

namespace WebApp.Controllers;

public class AuthController : Controller
{
    private readonly AuthService _auth;
    private readonly TaskService _tasks;

    public AuthController(AuthService auth, TaskService tasks)
    {
        _auth = auth;
        _tasks = tasks;
    }

    public IActionResult Login()
    {
        if (_auth.IsLoggedIn()) return RedirectToAction("Dashboard");
        return View();
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Login(LoginViewModel model)
    {
        if (!ModelState.IsValid) return View(model);

        var result = await _auth.LoginAsync(model.Email, model.Password);
        if (result.Success)
            return RedirectToAction("Dashboard");

        ModelState.AddModelError("", result.Error);
        return View(model);
    }

    public IActionResult Register()
    {
        if (_auth.IsLoggedIn()) return RedirectToAction("Dashboard");
        return View();
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Register(RegisterViewModel model)
    {
        if (!ModelState.IsValid) return View(model);

        var result = await _auth.RegisterAsync(model.Username, model.Email, model.Password);
        if (result.Success)
            return RedirectToAction("Login", new { registered = true });

        ModelState.AddModelError("", result.Error);
        return View(model);
    }

    [HttpPost]
    [ValidateAntiForgeryToken]
    public IActionResult Logout()
    {
        _auth.Logout();
        return RedirectToAction("Login");
    }

    public async Task<IActionResult> Dashboard()
    {
        var userId = _auth.GetUserId();
        if (userId == null) return RedirectToAction("Login");

        var counts = await _tasks.GetCountsAsync(userId.Value);

        ViewBag.Username = _auth.GetUsername();
        ViewBag.TotalTasks = counts.Total;
        ViewBag.PendingTasks = counts.Pending;
        ViewBag.CompletedTasks = counts.Completed;

        return View();
    }
}
