using Microsoft.EntityFrameworkCore;
using WebApp.Data;
using WebApp.Models;

namespace WebApp.Services;

public class AuthService
{
    private readonly AppDbContext _db;
    private readonly IHttpContextAccessor _http;

    public AuthService(AppDbContext db, IHttpContextAccessor http)
    {
        _db = db;
        _http = http;
    }

    public async Task<(bool Success, string Error)> RegisterAsync(string username, string email, string password)
    {
        if (await _db.Users.AnyAsync(u => u.Email == email))
            return (false, "El email ya está registrado.");

        if (await _db.Users.AnyAsync(u => u.Username == username))
            return (false, "El nombre de usuario ya existe.");

        var user = new User
        {
            Username = username,
            Email = email,
            Password = BCryptHash(password),
            CreatedAt = DateTime.UtcNow
        };

        _db.Users.Add(user);
        await _db.SaveChangesAsync();

        return (true, string.Empty);
    }

    public async Task<(bool Success, string Error)> LoginAsync(string email, string password)
    {
        var user = await _db.Users.FirstOrDefaultAsync(u => u.Email == email);

        if (user == null || !VerifyBCrypt(password, user.Password))
            return (false, "Credenciales inválidas.");

        _http.HttpContext!.Session.SetInt32("UserId", user.Id);
        _http.HttpContext!.Session.SetString("Username", user.Username);

        return (true, string.Empty);
    }

    public void Logout()
    {
        _http.HttpContext!.Session.Clear();
    }

    public bool IsLoggedIn()
    {
        return _http.HttpContext!.Session.GetInt32("UserId").HasValue;
    }

    public int? GetUserId()
    {
        return _http.HttpContext!.Session.GetInt32("UserId");
    }

    public string? GetUsername()
    {
        return _http.HttpContext!.Session.GetString("Username");
    }

    private static string BCryptHash(string password)
    {
        return BCrypt.Net.BCrypt.HashPassword(password, workFactor: 12);
    }

    private static bool VerifyBCrypt(string password, string hash)
    {
        return BCrypt.Net.BCrypt.Verify(password, hash);
    }
}
