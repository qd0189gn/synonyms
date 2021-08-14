package puzzle;

public class Movie {
	private String name = " ";
	private String year_made = " ";
	
	Movie (String name, String year_made2) {
		this.name = name;
		this.year_made = year_made2;
	}
	public String getName() {
		return name;
	}
	public String getYear_made() {
		return year_made;
	}
	public String toString() {
		return name + " " + year_made;
	}
}