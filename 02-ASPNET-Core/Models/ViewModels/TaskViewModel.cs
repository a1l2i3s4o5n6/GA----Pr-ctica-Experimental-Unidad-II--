using System.ComponentModel.DataAnnotations;

namespace WebApp.Models.ViewModels;

public class TaskViewModel
{
    public int Id { get; set; }

    [Required, MaxLength(200)]
    public string Title { get; set; } = string.Empty;

    public string Description { get; set; } = string.Empty;

    public string Status { get; set; } = "pending";

    public DateTime? DueDate { get; set; }
}
