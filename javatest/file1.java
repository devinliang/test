import java.io.*;
public class file1 {
  public static void main(String[] args) {
    try {
      FileReader fr=new FileReader("test.txt");
      BufferedReader br=new BufferedReader(fr);
      String line;
      while ((line=br.readLine()) != null) {
        System.out.println(line);
        }
      }
    catch (IOException e) {System.out.println(e);}
    }
  }